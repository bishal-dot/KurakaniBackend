<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Report or log an exception.
     */
    public function report(Throwable $exception): void
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Force JSON for API routes
        if ($request->expectsJson() || $request->is('api/*')) {
            $status = $exception instanceof HttpException ? $exception->getStatusCode() : 500;

            return response()->json([
                'error' => true,
                'message' => $exception->getMessage() ?: 'Server Error',
            ], $status);
        }

        // Fallback to Laravel's default HTML error page
        return parent::render($request, $exception);
    }

    /**
     * Handle unauthenticated users.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'error' => true,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        return redirect()->guest(route('login'));
    }
}
