<?php

/**
 * Laravel - Shared Hosting Index File
 * 
 * Copy this file to public_html/workcnt/index.php on your shared hosting server.
 * Adjust the paths below to match your server structure.
 */

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Set Laravel App Base Path
|--------------------------------------------------------------------------
|
| For shared hosting, the Laravel app folder is typically outside public_html.
| Adjust this path to point to your Laravel installation directory.
|
| Common structures:
| - /home/username/cnt-system           (app folder)
| - /home/username/public_html/workcnt  (public folder - where this file lives)
|
*/

// Adjust this path to match your server structure
$laravelAppPath = dirname(__DIR__, 2) . '/cnt-system';

// Alternative paths to try if the above doesn't work
if (!is_dir($laravelAppPath)) {
    $laravelAppPath = dirname(__DIR__) . '/cnt-system';
}
if (!is_dir($laravelAppPath)) {
    $laravelAppPath = '/home/' . get_current_user() . '/cnt-system';
}

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
*/

if (file_exists($maintenance = $laravelAppPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
*/

require $laravelAppPath.'/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
*/

$app = require_once $laravelAppPath.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
