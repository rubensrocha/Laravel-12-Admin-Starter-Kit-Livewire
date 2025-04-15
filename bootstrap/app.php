<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__.'/../routes/web.php',
        ],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        // Register admin routes
        then: function (){
            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            // Add admin require password middleware
            'admin.password.confirm' => \App\Http\Middleware\Admin\RequirePassword::class,
            // Override the default 'guest' authentication middleware
            'guest' => \App\Http\Middleware\Admin\RedirectIfAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle site and admin authentication redirect exceptions
        $exceptions->render(function(Throwable $throwable, \Illuminate\Http\Request $request) {
            if($throwable instanceof \Illuminate\Auth\AuthenticationException){
                $guard = Arr::first($throwable->guards());
                if ($guard === 'admin') {
                    return redirect()->route('admin.login');
                }
                return redirect()->route('login');
            }
        });
    })->create();
