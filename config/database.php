<?php
$host = 'localhost';
$dbname = 'gaya_sehat';
$username = 'root'; 
$password = '';     

$conn = mysqli_connect($host, $username,$password, $dbname);
try {
    $pdo = new PDO("mysql:host=$host;port=3306;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
