<?php
// ลบไฟล์นี้ทันทีหลังใช้งาน!
// วางไฟล์นี้ที่ public_html/clear-cache.php แล้วเปิด browser
require dirname(__DIR__) . '/vendor/autoload.php';
$app = require_once dirname(__DIR__) . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
echo "<pre>";
echo "Clearing caches...\n";
$kernel->call('config:clear');
echo "Config cleared!\n";
$kernel->call('cache:clear');
echo "Cache cleared!\n";
$kernel->call('view:clear');
echo "View cleared!\n";
$kernel->call('route:clear');
echo "Route cleared!\n";
echo "\nAll done! Please delete this file now.";
echo "</pre>";