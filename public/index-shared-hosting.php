<?php

/**
 * Laravel - Shared Hosting Index File
 *
 * โครงสร้าง server:
 *   /home/nassacth/domains/workcnt.nass.ac.th/           <- Laravel root
 *   /home/nassacth/domains/workcnt.nass.ac.th/public_html/  <- document root (วางไฟล์นี้ที่นี่ชื่อ index.php)
 *
 * วิธีใช้:
 *   1. copy ไฟล์นี้ไปวางที่ public_html/index.php
 *   2. copy ทุกไฟล์จาก public/ (ยกเว้น index.php เดิม) ไปวางที่ public_html/
 */

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Laravel App Base Path
|--------------------------------------------------------------------------
| public_html/ อยู่ใน Laravel root โดยตรง ดังนั้น dirname(__DIR__) = Laravel root
*/

$laravelAppPath = dirname(__DIR__);

/*
|--------------------------------------------------------------------------
| Sanity check
|--------------------------------------------------------------------------
*/
if (!is_dir($laravelAppPath . '/vendor')) {
    http_response_code(500);
    die('Laravel app path not found: ' . $laravelAppPath . ' — กรุณาตรวจสอบโครงสร้างไฟล์');
}

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
*/

if (file_exists($maintenance = $laravelAppPath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
*/

require $laravelAppPath . '/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
*/

$app = require_once $laravelAppPath . '/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
