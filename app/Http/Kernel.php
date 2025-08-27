<?php

namespace App\Http;

use App\Http\Middleware\RolePermissionMiddleware;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
  protected $routeMiddleware = [
    // otros middleware
    'role.permission' => RolePermissionMiddleware::class,
    'auth' => \App\Http\Middleware\AuthenticateSession::class,
  ];
}
