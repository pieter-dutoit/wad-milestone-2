<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

// https://laravel.com/docs/11.x/middleware#defining-middleware
// https://laravel.com/docs/11.x/middleware#middleware-parameters

class VerifyUserRole
{
    /**
     * Ensures a user has a valid role
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role1, $role2 = null): Response
    {

        if (in_array(Auth::user()->role->role, [$role1, $role2])) {
            return $next($request);
        }
        // https://laravel.com/docs/11.x/session#flash-data
        $request->session()->flash('warning', 'You do not have permission to view that page.');
        return redirect(route('enrolments.index'));
    }
}
