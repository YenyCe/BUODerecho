

<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "u224139272_buolistas";

$conn = new mysqli($host, $user, $pass, $dbname);

if($conn->connect_error){
    die("Error de conexión: " . $conn->connect_error);
}
?>
