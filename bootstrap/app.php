<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthenticationException;
use App\Exceptions\InvalidCredentialsException;
use App\Http\Middleware\BlockSwaggerInProduction;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
      $middleware->alias([
        'swagger.block' => BlockSwaggerInProduction::class,
      ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
      $exceptions->render(function (AuthenticationException $e, Request $request) {
        return response()->json([
            'error' => true,
            'message' => 'Token tidak valid atau tidak ditemukan',
        ], 401);
    });

    $exceptions->render(function (TokenExpiredException $e, Request $request) {
        return response()->json([
            'error' => true,
            'message' => 'Token sudah kadaluarsa',
        ], 401);
    });

    $exceptions->render(function (TokenInvalidException $e, Request $request) {
        return response()->json([
            'error' => true,
            'message' => 'Token tidak valid',
        ], 401);
    });

    $exceptions->render(function (JWTException $e, Request $request) {
        return response()->json([
            'error' => true,
            'message' => 'Token tidak ditemukan',
        ], 401);
    });

    $exceptions->render(function (InvalidCredentialsException $e, Request $request) {
      return response()->json([
          'error' => true,
          'message' => 'Username atau password tidak valid',
      ], 401);
    });

    })
    ->create();
