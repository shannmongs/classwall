<?php
// db.php - connects to PostgreSQL using PDO.
// Change the password to the one you set when installing PostgreSQL.

$host     = 'localhost';
$port     = '5432';
$dbname   = 'classwall';
$user     = 'postgres';
$password = 'your_password_here';

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
       die('Error: ' . $e->getMessage());
}
