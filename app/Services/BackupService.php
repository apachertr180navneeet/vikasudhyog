<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class BackupService
{
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        if (!File::exists($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }
    }

    /**
     * Get database metrics, table statistics, and health indicators.
     */
    public function getDatabaseStats(): array
    {
        $dbName = config('database.connections.mysql.database', 'vikasudhyog');
        $dbHost = config('database.connections.mysql.host', '127.0.0.1');
        $dbPort = config('database.connections.mysql.port', '3306');

        $mysqlVersion = '8.0';
        $totalSizeMb = 0.0;
        $totalTables = 0;
        $totalRows = 0;

        try {
            $verResult = DB::select("SELECT VERSION() as ver");
            if (!empty($verResult)) {
                $mysqlVersion = $verResult[0]->ver ?? '8.0';
            }

            $stats = DB::select("
                SELECT 
                    COUNT(*) as table_count,
                    COALESCE(SUM(data_length + index_length), 0) as total_bytes,
                    COALESCE(SUM(table_rows), 0) as total_rows
                FROM information_schema.TABLES 
                WHERE table_schema = ?
            ", [$dbName]);

            if (!empty($stats)) {
                $totalTables = (int) ($stats[0]->table_count ?? 0);
                $totalBytes = (float) ($stats[0]->total_bytes ?? 0);
                $totalSizeMb = round($totalBytes / (1024 * 1024), 2);
                $totalRows = (int) ($stats[0]->total_rows ?? 0);
            }
        } catch (\Throwable $e) {
            Log::warning('Could not retrieve DB statistics: ' . $e->getMessage());
        }

        $backups = $this->getBackupsList();
        $lastBackup = !empty($backups) ? $backups[0]['created_at'] : null;

        return [
            'database'         => $dbName,
            'host'             => "{$dbHost}:{$dbPort}",
            'version'          => $mysqlVersion,
            'size_mb'          => $totalSizeMb,
            'tables_count'     => $totalTables,
            'rows_count'       => $totalRows,
            'backups_count'    => count($backups),
            'last_backup_date' => $lastBackup,
        ];
    }

    /**
     * Retrieve all available local backup files.
     */
    public function getBackupsList(): array
    {
        if (!File::exists($this->backupDir)) {
            return [];
        }

        $files = File::files($this->backupDir);
        $backups = [];

        foreach ($files as $file) {
            $extension = strtolower($file->getExtension());
            if (!in_array($extension, ['sql', 'json'])) {
                continue;
            }

            $sizeBytes = $file->getSize();
            $backups[] = [
                'filename'    => $file->getFilename(),
                'filepath'    => $file->getRealPath(),
                'size_bytes'  => $sizeBytes,
                'size_human'  => $this->formatFileSize($sizeBytes),
                'type'        => strtoupper($extension),
                'created_at'  => Carbon::createFromTimestamp($file->getMTime()),
            ];
        }

        // Sort descending by creation date
        usort($backups, fn($a, $b) => $b['created_at']->timestamp <=> $a['created_at']->timestamp);

        return $backups;
    }

    /**
     * Create a database backup snapshot (SQL Dump or JSON).
     */
    public function createBackup(string $type = 'sql'): array
    {
        $timestamp = date('Y-m-d_His');
        $dbName = config('database.connections.mysql.database', 'vikasudhyog');

        if (strtolower($type) === 'json') {
            $filename = "VU_Backup_{$dbName}_{$timestamp}.json";
            $filepath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;
            $this->generateJsonBackup($filepath);
        } else {
            $filename = "VU_Backup_{$dbName}_{$timestamp}.sql";
            $filepath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;
            $this->generateSqlBackup($filepath);
        }

        $sizeBytes = File::size($filepath);

        return [
            'success'    => true,
            'filename'   => $filename,
            'filepath'   => $filepath,
            'size_human' => $this->formatFileSize($sizeBytes),
            'type'       => strtoupper($type),
            'message'    => "Database snapshot '{$filename}' created successfully (" . $this->formatFileSize($sizeBytes) . ")!",
        ];
    }

    /**
     * Generate pure PHP SQL dump.
     */
    protected function generateSqlBackup(string $filepath): void
    {
        $tables = DB::select('SHOW TABLES');
        $handle = fopen($filepath, 'w+');

        $dbName = config('database.connections.mysql.database', 'vikasudhyog');
        $date = date('Y-m-d H:i:s');

        // Write SQL Header
        fwrite($handle, "-- ==========================================================\n");
        fwrite($handle, "-- VIKAS UDHYOG ERP - Full Database Backup Dump\n");
        fwrite($handle, "-- Database: `{$dbName}`\n");
        fwrite($handle, "-- Date / Time: {$date}\n");
        fwrite($handle, "-- Platform: Laravel 11 / MySQL Engine\n");
        fwrite($handle, "-- ==========================================================\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";\n");
        fwrite($handle, "SET time_zone = \"+05:30\";\n\n");

        $dbProp = 'Tables_in_' . $dbName;

        foreach ($tables as $t) {
            // Support object property or array key
            $tableName = $t->{$dbProp} ?? array_values((array) $t)[0];

            fwrite($handle, "\n-- --------------------------------------------------------\n");
            fwrite($handle, "-- Table structure for table `{$tableName}`\n");
            fwrite($handle, "-- --------------------------------------------------------\n");
            fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");

            // Table Schema (DDL)
            $createTableRes = DB::select("SHOW CREATE TABLE `{$tableName}`");
            if (!empty($createTableRes)) {
                $createSql = $createTableRes[0]->{'Create Table'} ?? array_values((array) $createTableRes[0])[1];
                fwrite($handle, $createSql . ";\n\n");
            }

            // Dump Rows in chunks
            $rowsCount = DB::table($tableName)->count();
            if ($rowsCount > 0) {
                fwrite($handle, "-- Dumping data for table `{$tableName}` ({$rowsCount} rows)\n");

                DB::table($tableName)->orderBy(DB::raw('1'))->chunk(500, function ($rows) use ($handle, $tableName) {
                    if ($rows->isEmpty()) {
                        return;
                    }

                    $first = true;
                    $insertPrefix = "INSERT INTO `{$tableName}` VALUES ";
                    $valuesSql = [];

                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $escapedValues = array_map(function ($val) {
                            if (is_null($val)) {
                                return 'NULL';
                            }
                            // Escape special characters safely
                            return "'" . addslashes((string) $val) . "'";
                        }, array_values($rowArray));

                        $valuesSql[] = "(" . implode(", ", $escapedValues) . ")";
                    }

                    fwrite($handle, $insertPrefix . implode(",\n", $valuesSql) . ";\n");
                });

                fwrite($handle, "\n");
            }
        }

        // Write SQL Footer
        fwrite($handle, "\nSET FOREIGN_KEY_CHECKS=1;\n");
        fwrite($handle, "-- End of Vikas Udhyog Database Dump\n");
        fclose($handle);
    }

    /**
     * Generate JSON database archive.
     */
    protected function generateJsonBackup(string $filepath): void
    {
        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database', 'vikasudhyog');
        $dbProp = 'Tables_in_' . $dbName;

        $data = [
            'metadata' => [
                'app'        => 'Vikas Udhyog ERP',
                'created_at' => date('c'),
                'database'   => $dbName,
                'version'    => '1.0',
            ],
            'tables'   => [],
        ];

        foreach ($tables as $t) {
            $tableName = $t->{$dbProp} ?? array_values((array) $t)[0];
            $data['tables'][$tableName] = DB::table($tableName)->get()->toArray();
        }

        File::put($filepath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Restore database from an uploaded or existing backup file.
     */
    public function restoreFromFile(string $filepath): array
    {
        if (!File::exists($filepath)) {
            return ['success' => false, 'message' => 'Backup file does not exist on server.'];
        }

        $extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

        try {
            if ($extension === 'sql') {
                $this->executeSqlRestore($filepath);
            } elseif ($extension === 'json') {
                $this->executeJsonRestore($filepath);
            } else {
                return ['success' => false, 'message' => 'Unsupported backup file extension. Only .sql and .json are accepted.'];
            }

            // Clear compiled caches
            Artisan::call('optimize:clear');

            return [
                'success' => true,
                'message' => "Database restored successfully from backup file: " . basename($filepath),
            ];
        } catch (\Throwable $e) {
            Log::error('Restore failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'success' => false,
                'message' => 'Restore failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Execute SQL restore.
     */
    protected function executeSqlRestore(string $filepath): void
    {
        $sql = File::get($filepath);

        // Turn off foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Execute queries
        DB::unprepared($sql);

        // Turn on foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Execute JSON restore.
     */
    protected function executeJsonRestore(string $filepath): void
    {
        $json = json_decode(File::get($filepath), true);
        if (!$json || !isset($json['tables'])) {
            throw new \RuntimeException('Invalid JSON backup file structure.');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($json['tables'] as $tableName => $rows) {
            // Check if table exists
            $tableExists = DB::select("SHOW TABLES LIKE '{$tableName}'");
            if (!empty($tableExists)) {
                DB::table($tableName)->truncate();
                if (!empty($rows)) {
                    foreach (array_chunk($rows, 200) as $chunk) {
                        DB::table($tableName)->insert($chunk);
                    }
                }
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Delete a backup file.
     */
    public function deleteBackup(string $filename): bool
    {
        $safeName = basename($filename);
        $filepath = $this->backupDir . DIRECTORY_SEPARATOR . $safeName;

        if (File::exists($filepath)) {
            return File::delete($filepath);
        }

        return false;
    }

    /**
     * Format file size to human-readable string.
     */
    public function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}
