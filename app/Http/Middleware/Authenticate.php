<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (str($request->route()->uri())->contains("api") && !$request->ajax()) {
            return route('main-app');
        }

        return $request->expectsJson() ? null : route('login');
    }
}
