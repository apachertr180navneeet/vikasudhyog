<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'gstin',
        'pan',
        'phone',
        'email',
        'website',
        'address',
        'city',
        'state',
        'pincode',
        'financial_year',
        'bank_name',
        'bank_account_no',
        'bank_ifsc',
        'bank_branch',
        'tagline',
        'status',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Scope for active companies.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Set this company as the default and unset others.
     */
    public function makeDefault(): void
    {
        static::where('id', '!=', $this->id)->update(['is_default' => false]);
        $this->update(['is_default' => true]);
    }

    /**
     * Generate an intelligent unique short code from company name.
     */
    public static function generateUniqueCode(string $name, ?int $excludeId = null): string
    {
        $clean = preg_replace('/[^A-Za-z0-9\s]/', '', $name);
        $words = array_values(array_filter(explode(' ', trim((string)$clean))));

        if (empty($words)) {
            $base = 'COMP';
        } elseif (count($words) === 1) {
            $word = strtoupper($words[0]);
            $base = strlen($word) <= 5 ? $word : substr($word, 0, 4);
        } else {
            // If first word is short (e.g. "VU" or "Vikas"), get acronym of words
            $acronym = '';
            foreach ($words as $w) {
                if (!empty($w)) {
                    $acronym .= strtoupper($w[0]);
                }
            }

            if (strlen($acronym) >= 2) {
                $base = substr($acronym, 0, 6);
            } else {
                $base = strtoupper(substr($words[0], 0, 3));
            }
        }

        $base = strtoupper(preg_replace('/[^A-Z0-9]/', '', $base)) ?: 'COMP';

        $code = $base;
        $counter = 1;

        while (static::where('code', $code)->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $code = $base . '-' . sprintf('%02d', $counter);
            $counter++;
        }

        return $code;
    }
}
