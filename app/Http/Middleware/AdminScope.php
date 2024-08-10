<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin/login') && auth()->guest()) {
            return $next($request);
        }

        if (User::isAdmin()) {
            return $next($request);
        }

        if (User::isDosen()) {
            return redirect('/dosen');
        }

        abort(403);
    }
}
