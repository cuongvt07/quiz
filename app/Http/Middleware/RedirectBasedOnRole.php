<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectBasedOnRole
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Nếu là admin
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            
            // Nếu là user thường
            if (Auth::user()->role === 'user') {
                return redirect()->route('home');
            }
        }

        return $next($request);
    }
}