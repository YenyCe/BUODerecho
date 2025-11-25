<?php
class DocentesModel {

    private $conn;

    public function __construct($conexion){
        $this->conn = $conexion;
    }

    public function getDocentes(){
        $sql = "SELECT * FROM docentes ORDER BY id_docente DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function agregarDocente($nombre, $apellidos, $correo, $telefono){
        $stmt = $this->conn->prepare("INSERT INTO docentes (nombre, apellidos, correo, telefono) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $apellidos, $correo, $telefono);
        return $stmt->execute();
    }

    public function getDocente($id){
        $stmt = $this->conn->prepare("SELECT * FROM docentes WHERE id_docente=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function editarDocente($id, $nombre, $apellidos, $correo, $telefono){
        $stmt = $this->conn->prepare("UPDATE docentes SET nombre=?, apellidos=?, correo=?, telefono=? WHERE id_docente=?");
        $stmt->bind_param("ssssi", $nombre, $apellidos, $correo, $telefono, $id);
        return $stmt->execute();
    }

    public function eliminarDocente($id){
        $stmt = $this->conn->prepare("DELETE FROM docentes WHERE id_docente=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
