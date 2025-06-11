<?php



$host = 'localhost';
$port = '3307';
$dbname = 'world';
$charset = 'utf8mb4';
$user = 'root';
$password = 'CIEF1234';
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connection successful!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
} 