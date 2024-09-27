<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function render($request, Throwable $exception)
    {
        if($exception instanceof ModelNotFoundException) {
            
            if($request->route()->uri && \Str::contains($request->route()->uri, 'admin')) {
                if($request->user() && !$request->user()->isSuperAdmin()) {
                    // Unauthorized response
                    return response()->json([
                        'message' => "You don't authorize for this action."
                    ],403);
                }
            }

            // Not found response
            return response()->json([
                'message' => "Record Not Found!"
            ],404);
        }

        return parent::render($request, $exception);
    }
}
