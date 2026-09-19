<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccessLevelController extends Controller
{
    /**
     * Display the dynamic Access Level & Role Management screen.
     */
    public function index()
    {
        // Ensure standard built-in roles & permissions exist
        Role::seedDefaultsIfEmpty();

        $roles = Role::with('permissions')
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        // Calculate active user counts per role name
        $userCounts = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        // Format role permissions map for convenient frontend JSON consumption
        $rolesData = $roles->map(function ($r) use ($userCounts) {
            $permMap = [];
            foreach ($r->permissions as $p) {
                $permMap[$p->module_key] = [
                    'can_view'   => (bool) $p->can_view,
                    'can_add'    => (bool) $p->can_add,
                    'can_edit'   => (bool) $p->can_edit,
                    'can_delete' => (bool) $p->can_delete,
                    'can_export' => (bool) $p->can_export,
                ];
            }

            return [
                'id'          => $r->id,
                'name'        => $r->name,
                'slug'        => $r->slug,
                'icon'        => $r->icon ?? 'fa-user-gear',
                'color'       => $r->color ?? '#4F46E5',
                'bg'          => $r->bg ?? '#EEF2FF',
                'badge'       => $r->badge ?? ($r->is_system ? 'System Role' : 'Custom Role'),
                'description' => $r->description ?? '',
                'is_system'   => (bool) $r->is_system,
                'status'      => $r->status,
                'order'       => (int) $r->order,
                'users_count' => $userCounts[$r->name] ?? 0,
                'permissions' => $permMap,
            ];
        });

        return view('admin.masters.access-level', [
            'roles'         => $rolesData,
            'allModuleKeys' => Role::ALL_MODULE_KEYS,
        ]);
    }

    /**
     * Store a new custom role and clone/set default module permissions.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:60|unique:roles,name',
            'description'      => 'nullable|string|max:255',
            'badge'            => 'nullable|string|max:30',
            'color'            => 'nullable|string|max:20',
            'icon'             => 'nullable|string|max:50',
            'template_role_id' => 'nullable|exists:roles,id',
        ]);

        $name = trim($validated['name']);
        $slug = Str::slug($name);

        // Ensure unique slug
        $originalSlug = $slug;
        $counter = 1;
        while (Role::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $color = $validated['color'] ?? '#4F46E5';
        $icon = $validated['icon'] ?? 'fa-user-tag';
        $badge = $validated['badge'] ?? 'Custom Role';
        $description = !empty($validated['description']) ? $validated['description'] : 'Custom user defined role with tailored module permissions.';

        $maxOrder = Role::max('order') ?? 0;

        $role = Role::create([
            'name'        => $name,
            'slug'        => $slug,
            'icon'        => $icon,
            'color'       => $color,
            'bg'          => '#F8FAFC',
            'badge'       => $badge,
            'description' => $description,
            'is_system'   => false,
            'status'      => 'active',
            'order'       => $maxOrder + 1,
        ]);

        // If a template role was selected, clone its permissions; otherwise initialize with all false
        $templatePerms = [];
        if (!empty($validated['template_role_id'])) {
            $template = Role::with('permissions')->find($validated['template_role_id']);
            if ($template) {
                foreach ($template->permissions as $p) {
                    $templatePerms[$p->module_key] = [
                        'can_view'   => (bool) $p->can_view,
                        'can_add'    => (bool) $p->can_add,
                        'can_edit'   => (bool) $p->can_edit,
                        'can_delete' => (bool) $p->can_delete,
                        'can_export' => (bool) $p->can_export,
                    ];
                }
            }
        }

        $newPermMap = [];
        foreach (Role::ALL_MODULE_KEYS as $modKey) {
            $canView = $templatePerms[$modKey]['can_view'] ?? false;
            $canAdd = $templatePerms[$modKey]['can_add'] ?? false;
            $canEdit = $templatePerms[$modKey]['can_edit'] ?? false;
            $canDelete = $templatePerms[$modKey]['can_delete'] ?? false;
            $canExport = $templatePerms[$modKey]['can_export'] ?? false;

            $role->permissions()->create([
                'module_key' => $modKey,
                'can_view'   => $canView,
                'can_add'    => $canAdd,
                'can_edit'   => $canEdit,
                'can_delete' => $canDelete,
                'can_export' => $canExport,
            ]);

            $newPermMap[$modKey] = [
                'can_view'   => $canView,
                'can_add'    => $canAdd,
                'can_edit'   => $canEdit,
                'can_delete' => $canDelete,
                'can_export' => $canExport,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => "Role '{$role->name}' created successfully.",
            'role'    => [
                'id'          => $role->id,
                'name'        => $role->name,
                'slug'        => $role->slug,
                'icon'        => $role->icon,
                'color'       => $role->color,
                'bg'          => $role->bg,
                'badge'       => $role->badge,
                'description' => $role->description,
                'is_system'   => false,
                'status'      => 'active',
                'order'       => $role->order,
                'users_count' => 0,
                'permissions' => $newPermMap,
            ],
        ]);
    }

    /**
     * Update role details (name, description, badge, color, icon).
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        $rules = [
            'description' => 'nullable|string|max:255',
            'badge'       => 'nullable|string|max:30',
            'color'       => 'nullable|string|max:20',
            'icon'        => 'nullable|string|max:50',
        ];

        // If not a system role, allow renaming
        if (!$role->is_system) {
            $rules['name'] = 'required|string|max:60|unique:roles,name,' . $role->id;
        }

        $validated = $request->validate($rules);

        $oldName = $role->name;

        if (!$role->is_system && !empty($validated['name'])) {
            $role->name = trim($validated['name']);
            $role->slug = Str::slug($role->name);

            // Also update any assigned users to match the new role name
            if ($oldName !== $role->name) {
                User::where('role', $oldName)->update(['role' => $role->name]);
            }
        }

        if (isset($validated['description'])) {
            $role->description = $validated['description'];
        }
        if (!empty($validated['badge'])) {
            $role->badge = $validated['badge'];
        }
        if (!empty($validated['color'])) {
            $role->color = $validated['color'];
        }
        if (!empty($validated['icon'])) {
            $role->icon = $validated['icon'];
        }

        $role->save();

        return response()->json([
            'success' => true,
            'message' => "Role '{$role->name}' updated successfully.",
            'role'    => [
                'id'          => $role->id,
                'name'        => $role->name,
                'slug'        => $role->slug,
                'icon'        => $role->icon,
                'color'       => $role->color,
                'bg'          => $role->bg,
                'badge'       => $role->badge,
                'description' => $role->description,
                'is_system'   => (bool) $role->is_system,
                'status'      => $role->status,
                'order'       => (int) $role->order,
            ],
        ]);
    }

    /**
     * Delete a custom role from database.
     */
    public function destroy(Role $role): JsonResponse
    {
        if ($role->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'Built-in system roles cannot be deleted.',
            ], 422);
        }

        $usersCount = User::where('role', $role->name)->count();
        if ($usersCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete role '{$role->name}' because {$usersCount} user(s) are currently assigned to it. Please reassign them first.",
            ], 422);
        }

        $role->permissions()->delete();
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => "Role '{$role->name}' has been permanently deleted.",
        ]);
    }

    /**
     * Toggle role status between active and inactive.
     */
    public function toggleStatus(Role $role): JsonResponse
    {
        if ($role->name === 'Super Administrator') {
            return response()->json([
                'success' => false,
                'message' => 'Super Administrator role cannot be deactivated.',
            ], 422);
        }

        $role->status = ($role->status === 'active') ? 'inactive' : 'active';
        $role->save();

        return response()->json([
            'success' => true,
            'message' => "Role '{$role->name}' status changed to " . ucfirst($role->status) . '.',
            'status'  => $role->status,
        ]);
    }

    /**
     * Save/upsert granular permissions for the specified role.
     */
    public function savePermissions(Request $request, Role $role): JsonResponse
    {
        $permissions = $request->input('permissions', []);

        foreach (Role::ALL_MODULE_KEYS as $modKey) {
            $perm = $permissions[$modKey] ?? [];

            $canView   = !empty($perm['can_view']);
            $canAdd    = !empty($perm['can_add']);
            $canEdit   = !empty($perm['can_edit']);
            $canDelete = !empty($perm['can_delete']);
            $canExport = !empty($perm['can_export']);

            // Super Administrator always keeps full rights
            if ($role->name === 'Super Administrator') {
                $canView = $canAdd = $canEdit = $canDelete = $canExport = true;
            }

            RolePermission::updateOrCreate(
                [
                    'role_id'    => $role->id,
                    'module_key' => $modKey,
                ],
                [
                    'can_view'   => $canView,
                    'can_add'    => $canAdd,
                    'can_edit'   => $canEdit,
                    'can_delete' => $canDelete,
                    'can_export' => $canExport,
                ]
            );
        }

        // Return updated permissions map
        $role->load('permissions');
        $updatedPermMap = [];
        foreach ($role->permissions as $p) {
            $updatedPermMap[$p->module_key] = [
                'can_view'   => (bool) $p->can_view,
                'can_add'    => (bool) $p->can_add,
                'can_edit'   => (bool) $p->can_edit,
                'can_delete' => (bool) $p->can_delete,
                'can_export' => (bool) $p->can_export,
            ];
        }

        return response()->json([
            'success'     => true,
            'message'     => "Permissions for '{$role->name}' saved successfully to database.",
            'permissions' => $updatedPermMap,
        ]);
    }

    /**
     * Reset role permissions to blueprint defaults.
     */
    public function resetDefaults(Role $role): JsonResponse
    {
        // Find blueprint matching role name
        $blueprint = null;
        foreach (Role::SYSTEM_ROLES_BLUEPRINT as $item) {
            if (strcasecmp($item['name'], $role->name) === 0 || $item['slug'] === $role->slug) {
                $blueprint = $item;
                break;
            }
        }

        $defaults = $blueprint['defaults'] ?? [];
        $isSuperAdmin = ($role->name === 'Super Administrator');

        foreach (Role::ALL_MODULE_KEYS as $modKey) {
            $isAllowed = in_array($modKey, $defaults);

            RolePermission::updateOrCreate(
                [
                    'role_id'    => $role->id,
                    'module_key' => $modKey,
                ],
                [
                    'can_view'   => $isSuperAdmin || $isAllowed,
                    'can_add'    => $isSuperAdmin || ($isAllowed && !in_array($modKey, ['access_level', 'backup_restore'])),
                    'can_edit'   => $isSuperAdmin || ($isAllowed && !in_array($modKey, ['access_level', 'backup_restore'])),
                    'can_delete' => $isSuperAdmin || ($isAllowed && in_array($role->name, ['Super Administrator', 'Admin'])),
                    'can_export' => $isSuperAdmin || $isAllowed,
                ]
            );
        }

        $role->load('permissions');
        $updatedPermMap = [];
        foreach ($role->permissions as $p) {
            $updatedPermMap[$p->module_key] = [
                'can_view'   => (bool) $p->can_view,
                'can_add'    => (bool) $p->can_add,
                'can_edit'   => (bool) $p->can_edit,
                'can_delete' => (bool) $p->can_delete,
                'can_export' => (bool) $p->can_export,
            ];
        }

        return response()->json([
            'success'     => true,
            'message'     => "Permissions for '{$role->name}' have been reset to system defaults.",
            'permissions' => $updatedPermMap,
        ]);
    }
}
