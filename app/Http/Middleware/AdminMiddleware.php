<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login.form')
                ->with('error', 'You must be logged in.');
        }

        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')
                ->with('error', 'Access denied.');
        }

        return $next($request);
    }
}

