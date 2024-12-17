<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\HandleErrors;
use App\Http\Middleware\JwtMiddleware;
use App\Exceptions\Handler;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        #$middleware->append(JwtMiddleware::class);
        $middleware->append(HandleErrors::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (Response $response, ValidationException $exception) {
            if ($response->getStatusCode() === 422) {
                $errors = [];

                foreach ($exception->errors() as $key => $value) {
                    $error_obj = collect($value);
                    $n = new \stdClass();
                    $n->field = $key;
                    $n->error = $error_obj->first();
                    $errors[] = $n;
                }

                return errorResponse( compact('errors'), 'Validation Error!',Response::HTTP_UNPROCESSABLE_ENTITY,false);
            }

            return $response;
        });
    })->create();
