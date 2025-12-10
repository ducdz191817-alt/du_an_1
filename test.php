<?php
// Test 1: Kiểm tra đường dẫn
echo "__DIR__ = " . __DIR__ . "<br>";
echo "dirname(__DIR__) = " . dirname(__DIR__) . "<br>";
echo "BASE_PATH sẽ là = " . dirname(__DIR__) . "<br><br>";

// Test 2: Kiểm tra file helpers
$helpersPath = dirname(__DIR__) . '/src/helpers/helpers.php';
echo "Helpers path = " . $helpersPath . "<br>";
echo "File exists = " . (file_exists($helpersPath) ? 'YES' : 'NO') . "<br><br>";

// Test 3: Kiểm tra cấu trúc thư mục
echo "Cấu trúc thư mục:<br>";
$base = dirname(__DIR__);
$dirs = [
    $base . '/src/helpers/',
    $base . '/src/helpers/helpers.php',
    $base . '/src/helpers/database.php',
];

foreach ($dirs as $dir) {
    echo $dir . " => " . (file_exists($dir) ? 'EXISTS' : 'MISSING') . "<br>";
}