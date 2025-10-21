<?php


namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
  protected $middleware = [
    \Illuminate\Http\Middleware\HandleCors::class,
  ];

  protected $routeMiddleware = [
    'auth' => \App\Http\Middleware\AuthenticateSession::class,
    'role.permission' => \App\Http\Middleware\RolePermissionMiddleware::class,
  ];
}
