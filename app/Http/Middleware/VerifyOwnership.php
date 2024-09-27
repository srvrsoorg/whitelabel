<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Get the authenticated user
        $user = auth()->user();
        
        // Check if the server belongs to the authenticated user
        if($server = $request->route('server')) {
            if(!$user->servers()->where('id', $server->id)->exists()) {
                return response()->json(['error' => 'Unauthorized access to server.'], 403);
            }
        }

        return $next($request);
    }
}
