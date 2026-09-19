<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Standard ERP User Roles
     */
    public const ROLES = [
        'Super Administrator' => 'Full unrestricted system access, company setups & configurations',
        'Admin'               => 'Administrative access to all masters, transactions & reports',
        'Manager'             => 'Operations management, approval workflows & reports',
        'Accountant'          => 'Financial vouchers, accounts ledger, tax & audit registers',
        'Sales Manager'       => 'Customer orders, dispatching, sales invoices & tracking',
        'Purchase Manager'    => 'Vendor orders, weighbridge entries & inward goods',
        'Inventory Operator'  => 'Stock adjustments, item tracking & warehouse monitoring',
        'Staff'               => 'General data entry and view permissions',
    ];

    /**
     * Display a listing of system users with filtering & statistical KPI counters.
     */
    public function index(Request $request)
    {
        $query = User::with('company');

        // Search Filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Role Filter
        if ($role = $request->input('role')) {
            if ($role !== 'all') {
                $query->where('role', $role);
            }
        }

        // Company Filter
        if ($companyId = $request->input('company_id')) {
            if ($companyId !== 'all') {
                if ($companyId === 'global') {
                    $query->whereNull('company_id');
                } else {
                    $query->where('company_id', $companyId);
                }
            }
        }

        // Status Filter
        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        $users = $query->orderBy('id', 'asc')
                       ->paginate(10)
                       ->withQueryString();

        // Statistical KPI Counters
        $stats = [
            'total'        => User::count(),
            'active'       => User::where('status', 'active')->count(),
            'inactive'     => User::whereIn('status', ['inactive', 'suspended'])->count(),
            'super_admins' => User::whereIn('role', ['Super Administrator', 'Super Admin'])->count(),
        ];

        $companies = Company::orderBy('name')->get();
        $roles = self::ROLES;

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'users'   => $users,
                'stats'   => $stats,
            ]);
        }

        return view('admin.masters.user.index', [
            'pageTitle' => 'User Master - VIKAS UDHYOG ERP',
            'pageCode'  => 'master-user',
            'users'     => $users,
            'stats'     => $stats,
            'companies' => $companies,
            'roles'     => $roles,
            'filters'   => [
                'search'     => $request->input('search', ''),
                'role'       => $request->input('role', 'all'),
                'company_id' => $request->input('company_id', 'all'),
                'status'     => $request->input('status', 'all'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new user account.
     */
    public function create()
    {
        $companies = Company::orderBy('name')->get();
        $roles = self::ROLES;

        return view('admin.masters.user.create', [
            'pageTitle' => 'Add New User - VIKAS UDHYOG ERP',
            'pageCode'  => 'master-user-create',
            'companies' => $companies,
            'roles'     => $roles,
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:150',
            'username'   => 'required|string|max:50|alpha_dash|unique:users,username',
            'email'      => 'required|email|max:150|unique:users,email',
            'phone'      => 'nullable|string|max:25',
            'role'       => 'required|string|max:50',
            'company_id' => 'nullable|exists:companies,id',
            'password'   => 'required|string|min:6|confirmed',
            'status'     => 'nullable|in:active,inactive,suspended',
        ], [
            'username.alpha_dash' => 'Username may only contain letters, numbers, dashes and underscores.',
            'password.confirmed'  => 'Password confirmation does not match.',
            'company_id.exists'   => 'Selected company plant is invalid.',
        ]);

        $validated['status'] = $validated['status'] ?? 'active';
        $validated['username'] = strtolower(trim($validated['username']));
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User account created successfully!',
                'user'    => $user->load('company'),
            ]);
        }

        return redirect()->route('admin.masters.user')
            ->with('success', 'User account "' . $user->name . ' (' . $user->username . ')" created successfully.');
    }

    /**
     * Display the specified user profile.
     */
    public function show(Request $request, User $user)
    {
        $user->load('company');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'user'    => $user,
            ]);
        }

        return view('admin.masters.user.show', [
            'pageTitle' => $user->name . ' - User Profile',
            'pageCode'  => 'master-user',
            'user'      => $user,
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $companies = Company::orderBy('name')->get();
        $roles = self::ROLES;

        return view('admin.masters.user.edit', [
            'pageTitle' => 'Edit ' . $user->name . ' - User Master',
            'pageCode'  => 'master-user-edit',
            'user'      => $user,
            'companies' => $companies,
            'roles'     => $roles,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:150',
            'username'   => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($user->id)],
            'email'      => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'      => 'nullable|string|max:25',
            'role'       => 'required|string|max:50',
            'company_id' => 'nullable|exists:companies,id',
            'password'   => 'nullable|string|min:6|confirmed',
            'status'     => 'nullable|in:active,inactive,suspended',
        ], [
            'username.alpha_dash' => 'Username may only contain letters, numbers, dashes and underscores.',
            'password.confirmed'  => 'Password confirmation does not match.',
            'company_id.exists'   => 'Selected company plant is invalid.',
        ]);

        if (isset($validated['status'])) {
            // Guard: Prevent deactivating the only super admin
            if ($user->isSuperAdmin() && $validated['status'] !== 'active') {
                $otherSuperAdmins = User::where('id', '!=', $user->id)
                    ->whereIn('role', ['Super Administrator', 'Super Admin'])
                    ->where('status', 'active')
                    ->count();

                if ($otherSuperAdmins === 0) {
                    return back()->withInput()->with('error', 'Cannot deactivate the only active Super Administrator in the system.');
                }
            }
        } else {
            unset($validated['status']);
        }

        $validated['username'] = strtolower(trim($validated['username']));

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User account updated successfully!',
                'user'    => $user->fresh()->load('company'),
            ]);
        }

        return redirect()->route('admin.masters.user')
            ->with('success', 'User account "' . $user->name . '" updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user)
    {
        // Guard: Prevent deleting own logged in account
        if (Auth::id() === $user->id) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own active login account.',
                ], 422);
            }
            return back()->with('error', 'You cannot delete your own active login account.');
        }

        // Guard: Prevent deleting the only Super Administrator
        if ($user->isSuperAdmin()) {
            $otherSuperAdmins = User::where('id', '!=', $user->id)
                ->whereIn('role', ['Super Administrator', 'Super Admin'])
                ->count();

            if ($otherSuperAdmins === 0) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete the only Super Administrator in the system.',
                    ], 422);
                }
                return back()->with('error', 'Cannot delete the only Super Administrator in the system.');
            }
        }

        $userName = $user->name;
        $user->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User account deleted successfully!',
            ]);
        }

        return redirect()->route('admin.masters.user')
            ->with('success', 'User account "' . $userName . '" has been deleted.');
    }

    /**
     * Toggle user status (active / inactive).
     */
    public function toggleStatus(Request $request, User $user)
    {
        // Guard: Prevent deactivating own logged in account
        if (Auth::id() === $user->id && $user->status === 'active') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot deactivate your own active login account.',
                ], 422);
            }
            return back()->with('error', 'You cannot deactivate your own active login account.');
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User status changed to ' . ucfirst($newStatus),
                'status'  => $newStatus,
            ]);
        }

        return back()->with('success', 'User "' . $user->name . '" status changed to ' . ucfirst($newStatus) . '.');
    }
}
