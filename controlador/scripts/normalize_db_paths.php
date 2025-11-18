<?php
// Standalone DB normalization script
// Removes path prefixes from foto columns to store only basenames
// Run this if the migration script didn't connect to DB

@set_error_handler(function () {});
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'inventory_db';
$conn = @new mysqli($host, $user, $pass, $db);

if (!$conn || $conn->connect_error) {
    die("Cannot connect to database. Ensure MySQL is running on localhost.\n");
}

echo "Normalizing DB paths...\n";

// Clean productos.foto: remove all path prefixes, keep basename only
$result = $conn->query("SELECT COUNT(*) as cnt FROM productos WHERE foto LIKE '%/%'");
$row = $result->fetch_assoc();
$hasPrefix = $row['cnt'];

if ($hasPrefix > 0) {
    echo " - Found $hasPrefix productos with path prefixes\n";
    $conn->query("UPDATE productos SET foto = SUBSTRING_INDEX(foto, '/', -1) WHERE foto LIKE '%/%'");
    echo " - Cleaned productos.foto\n";
}

// Clean usuarios.foto
$result = $conn->query("SELECT COUNT(*) as cnt FROM usuarios WHERE foto LIKE '%/%'");
$row = $result->fetch_assoc();
$hasPrefix = $row['cnt'];

if ($hasPrefix > 0) {
    echo " - Found $hasPrefix usuarios with path prefixes\n";
    $conn->query("UPDATE usuarios SET foto = SUBSTRING_INDEX(foto, '/', -1) WHERE foto LIKE '%/%'");
    echo " - Cleaned usuarios.foto\n";
}

echo "Normalization complete.\n";
