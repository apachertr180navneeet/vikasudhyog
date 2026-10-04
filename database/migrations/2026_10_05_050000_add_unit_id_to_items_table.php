<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('category')->constrained('units')->nullOnDelete();
        });

        // Automatically backfill unit_id based on matching unit code or name
        if (Schema::hasTable('units')) {
            $units = DB::table('units')->whereNull('deleted_at')->get();
            foreach ($units as $u) {
                $code = strtoupper(trim($u->code ?? ''));
                $name = strtoupper(trim($u->name ?? ''));
                
                DB::table('items')
                    ->whereNull('unit_id')
                    ->where(function ($query) use ($code, $name) {
                        if (!empty($code)) {
                            $query->whereRaw('UPPER(TRIM(unit)) = ?', [$code]);
                        }
                        if (!empty($name)) {
                            $query->orWhereRaw('UPPER(TRIM(unit)) = ?', [$name]);
                        }
                    })
                    ->update(['unit_id' => $u->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');
        });
    }
};
