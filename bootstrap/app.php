<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);
        $middleware->alias([
            'admin'       => \App\Http\Middleware\AdminMiddleware::class,
            'super_admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Render HTTP errors (403, 404, 500, etc.) via Inertia Error page
        $exceptions->respond(function (\Illuminate\Http\Response $response) {
            if (in_array($response->status(), [403, 404, 419, 500, 503]) && !request()->expectsJson()) {
                return Inertia::render('Error', ['status' => $response->status()])
                    ->toResponse(request())
                    ->setStatusCode($response->status());
            }
            return $response;
        });
    })->create();
