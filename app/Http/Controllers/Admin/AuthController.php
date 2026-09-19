<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Display the admin login view.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle authentication for admin/login with brute-force protection.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string|max:100',
            'password' => 'required|string|min:6|max:100',
        ]);

        $username = trim($credentials['username']);
        $password = $credentials['password'];
        $remember = $request->boolean('remember');

        // Throttle key based on username and client IP
        $throttleKey = Str::transliterate(Str::lower($username).'|'.$request->ip());

        // Check if user is temporarily locked out (5 attempts allowed per 60 seconds)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->withInput($request->only('username', 'remember'))
                ->with('error', "Too many failed login attempts. Please try again in {$seconds} seconds.");
        }

        // Query user by username, email, or name
        $user = User::where('username', $username)
            ->orWhere('email', $username)
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            // Verify active status
            if (!$user->isActive()) {
                RateLimiter::hit($throttleKey);
                return back()
                    ->withInput($request->only('username', 'remember'))
                    ->with('error', 'Your account has been deactivated or suspended. Please contact the administrator.');
            }

            // Clear login attempt limiter on success
            RateLimiter::clear($throttleKey);

            // Log the user into Laravel's auth guard
            Auth::login($user, $remember);

            // Protect against session fixation
            $request->session()->regenerate();

            // Update login audit telemetry
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            // Synchronize session details for rapid access
            session([
                'vu_logged_in'  => true,
                'vu_user_id'    => $user->id,
                'vu_user_name'  => $user->name,
                'vu_user_role'  => $user->role ?? 'Administrator',
                'vu_user_email' => $user->email,
            ]);

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Welcome back, ' . $user->name . '! Logged in successfully.');
        }

        // Record failed attempt
        RateLimiter::hit($throttleKey);

        return back()
            ->withInput($request->only('username', 'remember'))
            ->with('error', 'Invalid username/email or password. Please check your credentials and try again.');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('info', 'You have been safely logged out.');
    }
}
