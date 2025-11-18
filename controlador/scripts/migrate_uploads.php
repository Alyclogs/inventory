<?php
// Migration script: move existing uploads into assets/uploads
// and normalize DB foto fields to store only the filename (basename)

// Try to connect to DB, but do not fatal on error - we still want to move files
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'inventory_db';
$conn = @new mysqli($host, $user, $pass, $db);
if ($conn && $conn->connect_error) {
    // connection failed - null out $conn so we skip DB updates later
    $conn = null;
}

function ensure_dir($path)
{
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

$base = __DIR__ . '/../../';
$destDir = $base . 'assets/uploads/';
ensure_dir($destDir);

$sources = [
    '../productos/uploads' => 'productos',
    '../usuarios/uploads' => 'usuarios'
];

$report = [];

foreach ($sources as $relDir => $table) {
    $srcDir = $base . $relDir . '/';
    if (!is_dir($srcDir)) {
        $report[$relDir] = 'source_not_found';
        continue;
    }

    $files = array_values(array_filter(scandir($srcDir), function ($f) {
        return $f !== '.' && $f !== '..';
    }));
    $moved = 0;

    foreach ($files as $file) {
        $src = $srcDir . $file;
        if (!is_file($src)) continue;

        $dst = $destDir . basename($file);
        // Avoid overwrite: if exists, append a counter
        $i = 1;
        $baseName = pathinfo($dst, PATHINFO_FILENAME);
        $ext = pathinfo($dst, PATHINFO_EXTENSION);
        while (file_exists($dst)) {
            $dst = $destDir . $baseName . '_' . $i . ($ext ? '.' . $ext : '');
            $i++;
        }

        if (@rename($src, $dst)) {
            $moved++;
        } else {
            // try copy then unlink
            if (@copy($src, $dst)) {
                @unlink($src);
                $moved++;
            }
        }
    }

    // Normalize DB foto fields for this table: keep basename only
    if ($conn) {
        if ($table === 'productos') {
            $conn->query("UPDATE productos SET foto = TRIM(REPLACE(REPLACE(REPLACE(foto, 'productos/uploads/', ''), 'assets/uploads/', ''), 'uploads/', ''))");
        } elseif ($table === 'usuarios') {
            $conn->query("UPDATE usuarios SET foto = TRIM(REPLACE(REPLACE(REPLACE(foto, 'usuarios/uploads/', ''), 'assets/uploads/', ''), 'uploads/', ''))");
        }
    }

    $report[$relDir] = [
        'scanned' => count($files),
        'moved' => $moved
    ];
}

echo "Migration completed:\n";
foreach ($report as $k => $v) {
    echo " - $k: ";
    if (is_array($v)) {
        echo "scanned={$v['scanned']} moved={$v['moved']}\n";
    } else {
        echo "$v\n";
    }
}
