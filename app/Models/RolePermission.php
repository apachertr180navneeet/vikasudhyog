<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'module_key',
        'can_view',
        'can_add',
        'can_edit',
        'can_delete',
        'can_export',
    ];

    protected $casts = [
        'can_view'   => 'boolean',
        'can_add'    => 'boolean',
        'can_edit'   => 'boolean',
        'can_delete' => 'boolean',
        'can_export' => 'boolean',
    ];

    /**
     * Parent role relationship.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
