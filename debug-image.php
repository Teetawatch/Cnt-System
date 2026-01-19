<?php

require __DIR__ . '/../workcnt-core/vendor/autoload.php';
$app = require_once __DIR__ . '/../workcnt-core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<h1>Image Troubleshooting Debugger</h1>";

// 1. Check Configuration
$configPublicPath = public_path();
$serverPublicPath = $_SERVER['DOCUMENT_ROOT'] . '/workcnt'; // Calculated from server var
$correctPath = '/home/nassacth/domains/nass.ac.th/public_html/workcnt';

echo "<h2>1. Path Configuration</h2>";
echo "<p><strong>Configured Public Path (Laravel):</strong> $configPublicPath</p>";
echo "<p><strong>Correct Public Path (Server):</strong> $correctPath</p>";

if ($configPublicPath == $correctPath) {
    echo "<p style='color:green; font-weight:bold;'>✅ Path Configuration is CORRECT.</p>";
} else {
    echo "<p style='color:red; font-weight:bold;'>❌ Path Configuration is WRONG.</p>";
    echo "<p>Please verify that <code>AppServiceProvider.php</code> is uploaded and cache is cleared.</p>";
}

// 2. Check Specific File
$filename = '1768276740_test11.jpg'; // The file you mentioned
$targetFile = $correctPath . '/uploads/staff-photos/' . $filename;
$wrongFile = '/home/nassacth/domains/nass.ac.th/workcnt-core/public/uploads/staff-photos/' . $filename;

echo "<h2>2. Searching for File: $filename</h2>";

echo "<ul>";
echo "<li>Checking Correct Location: <code>$targetFile</code> ... ";
if (file_exists($targetFile)) {
    echo "<span style='color:green; font-weight:bold;'>FOUND ✅</span>";
    echo " <br>(Perms: " . substr(sprintf('%o', fileperms($targetFile)), -4) . ")";
} else {
    echo "<span style='color:red; font-weight:bold;'>NOT FOUND ❌</span>";
}
echo "</li>";

echo "<li>Checking Wrong Location (Core): <code>$wrongFile</code> ... ";
if (file_exists($wrongFile)) {
    echo "<span style='color:orange; font-weight:bold;'>FOUND HERE INSTEAD ⚠️</span>";
    echo "<br>The file was saved to the wrong folder. The AppServiceProvider fix is NOT active.";
} else {
    echo "<span>NOT FOUND</span>";
}
echo "</li>";
echo "</ul>";

// 3. Check Directory Permissions
$uploadDir = $correctPath . '/uploads/staff-photos';
echo "<h2>3. Directory Status</h2>";
if (is_dir($uploadDir)) {
    echo "<p>Directory <code>$uploadDir</code> exists.</p>";
    echo "<p>Writable: " . (is_writable($uploadDir) ? "<span style='color:green'>YES ✅</span>" : "<span style='color:red'>NO ❌</span>") . "</p>";
    echo "<p>Permissions: " . substr(sprintf('%o', fileperms($uploadDir)), -4) . "</p>";
    
    // List files
    $files = scandir($uploadDir);
    echo "<h3>Files in correct folder:</h3><ul>";
    foreach($files as $f) {
        if($f != '.' && $f != '..') echo "<li>$f</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color:red'>Directory <code>$uploadDir</code> DOES NOT EXIST.</p>";
    echo "<p>Attempting to create it...</p>";
    try {
        mkdir($uploadDir, 0755, true);
        echo "<p style='color:green'>Created directory successfully.</p>";
    } catch (Exception $e) {
        echo "<p style='color:red'>Failed to create directory: " . $e->getMessage() . "</p>";
    }
}
