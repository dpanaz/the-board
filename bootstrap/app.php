<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
 ->withRouting(
  commands: __DIR__.'/../routes/console.php',
  health: '/up',
  then: fn () => Route::middleware('web')->group(base_path('routes/board.php')),
 )
 ->withMiddleware(function (Middleware $middleware): void {
  $middleware->redirectGuestsTo(fn (Request $request): string => route('board.login'));
 })
 ->withExceptions(fn (Exceptions $exceptions) => $exceptions->shouldRenderJsonWhen(
  fn (Request $request) => $request->is('api/*') || $request->expectsJson()
 ))->create();
