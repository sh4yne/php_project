<?php
// config.php - DB connection
$host = '127.0.0.1';
$db   = 'digital_library';
$user = 'root';
$pass = ''; // default XAMPP MySQL password is empty
$charset = 'utf8mb4';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
    die("DB connection failed: " . $mysqli->connect_error);
}
$mysqli->set_charset($charset);

// helper to escape output
function e($s){
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}
