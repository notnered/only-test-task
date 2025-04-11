<?php

$env = parse_ini_file('.env');

$DB_HOST = $env['DB_HOST'];
$DB_NAME = $env['DB_NAME'];
$DB_USER = $env['DB_USER'];
$DB_PASSWORD = $env['DB_PASSWORD'];

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", "$DB_USER", "$DB_PASSWORD");
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}

?>