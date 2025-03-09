<?php
namespace App\Http\Middleware;

use Closure;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get the authenticated user
        $user = Auth::user();

        // Check if the user has the correct role (role_id)
        if ($user->role->name !== $role) {
            return response()->json(['error' => 'Forbidden: You do not have the required role'], 403);
        }

        return $next($request);
    }
}
