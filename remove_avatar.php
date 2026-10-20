<?php
$files = [
    'resources/views/partials/navbar.blade.php',
    'resources/views/employee/dashboard.blade.php',
    'resources/views/admin/products/index.blade.php',
    'resources/views/admin/departments/index.blade.php',
    'resources/views/admin/delivery_notes/index.blade.php',
];
foreach($files as $file) {
    if(!file_exists($file)) continue;
    $content = file_get_contents($file);
    $content = preg_replace('/<div[^>]*class=\"[^\"]*avatar-sm[^\"]*\"[^>]*>.*?<\/div>/is', '', $content);
    file_put_contents($file, $content);
    echo "Processed $file\n";
}
