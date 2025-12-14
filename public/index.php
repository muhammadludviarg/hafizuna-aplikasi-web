<?php

//use Illuminate\Foundation\Application;
//use Illuminate\Http\Request;

//define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
//if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
//  require $maintenance;
//}

// Register the Composer autoloader...
//require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
//** @var Application $app */
//$app = require_once __DIR__.'/../bootstrap/app.php';

//$app->handleRequest(Request::capture());

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
} elseif (file_exists(__DIR__ . '/../hafizuna_core/vendor/autoload.php')) {
    require __DIR__ . '/../hafizuna_core/vendor/autoload.php';
    $app = require_once __DIR__ . '/../hafizuna_core/bootstrap/app.php';
} else {
    require __DIR__ . '/../laravel_app/vendor/autoload.php';
    $app = require_once __DIR__ . '/../laravel_app/bootstrap/app.php';
}


$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);