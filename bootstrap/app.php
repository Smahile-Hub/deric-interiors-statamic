<?php

use App\Support\ControlPanelSafety;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Statamic\Exceptions\StatamicProRequiredException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (StatamicProRequiredException $exception, $request) {
            if (! $request->is(trim(config('statamic.cp.route', 'cp'), '/').'*')) {
                return null;
            }

            if (ControlPanelSafety::recoverStaleUserCache()) {
                return redirect()->to($request->fullUrl());
            }

            return response()->view('errors.control-panel-unavailable', status: 503);
        });
    })->create();
