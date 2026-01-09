<?php
$host = "localhost";
$user = "u224139272_buoadmin";
$pass = "BUOlistas2026";
$dbname = "u224139272_buolistas";

$conn = new mysqli($host, $user, $pass, $dbname);

if($conn->connect_error){
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
