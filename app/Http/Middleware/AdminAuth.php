<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('admin.login')->with('error', 'Please log in to access the ERP administration.');
        }

        $user = Auth::user();
        if (!$user->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login')->with('error', 'Your account has been deactivated. Please contact the administrator.');
        }

        // Synchronize helper session variables
        if (!session('vu_logged_in') || session('vu_user_id') !== $user->id) {
            session([
                'vu_logged_in'  => true,
                'vu_user_id'    => $user->id,
                'vu_user_name'  => $user->name,
                'vu_user_role'  => $user->role ?? 'Administrator',
                'vu_user_email' => $user->email,
            ]);
        }

        return $next($request);
    }
}
