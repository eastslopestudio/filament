<?php

declare(strict_types = 1);

namespace Filament\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function authenticate(Request $request, array $guards)
    {
        $guard = config('filament.auth.guard');

        if ($this->auth->guard($guard)->check()) {
            abort_unless($this->auth->guard($guard)->user()->canAccessFilament(), 403);

            return $this->auth->shouldUse($guard);
        }

        $this->unauthenticated($request, $guards);
    }

    protected function redirectTo(Request $request)
    {
        if (! $request->expectsJson()) {
            return route('filament.auth.login');
        }
    }
}
