<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureIsPatient
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->patient) {
            return redirect()->route('dashboard')
                ->with('error', 'Esta área es exclusiva para pacientes registrados.');
        }

        return $next($request);
    }
}
