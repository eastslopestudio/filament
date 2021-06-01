<?php

declare(strict_types = 1);

namespace Filament\Http\Middleware;

use Closure;
use Filament\Filament;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, array ...$guards)
    {
        if (Filament::auth()->check()) {
            return redirect()->route('filament.dashboard');
        }

        return $next($request);
    }
}
