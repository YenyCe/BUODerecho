
<?php
session_start();
require_once "../config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1){

        $data = $result->fetch_assoc();

        // Validar si usuario está activo
        if($data['estado'] != 1){
            header("Location: ../views/login.php?inactivo=1");
            exit();
        }

        // Validar contraseña
        if(password_verify($password, $data['password'])){

            // Guardar sesión
            $_SESSION['id_usuario']  = $data['id_usuario'];
            $_SESSION['nombre']      = $data['nombre'];
            $_SESSION['rol']         = $data['rol'];
            $_SESSION['id_carrera']  = $data['id_carrera']; // Solo coordinadores lo tienen

            header("Location: ../views/inicio.php");

            exit();
        }
    }

    header("Location: ../views/login.php?error=1");
    exit();
}
?>
