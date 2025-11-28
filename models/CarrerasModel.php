<?php
class CarrerasModel {

    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

public function obtenerCarreras() {
    $sql = "SELECT * FROM carreras ORDER BY id_carrera ASC";
    $result = $this->conn->query($sql);
    return $result;
}

    public function agregarCarrera($nombre, $clave) {
        $stmt = $this->conn->prepare("INSERT INTO carreras (nombre, clave) VALUES (?, ?)");
        $stmt->bind_param("ss", $nombre, $clave);
        return $stmt->execute();
    }

    public function obtenerCarrera($id) {
        $stmt = $this->conn->prepare("SELECT * FROM carreras WHERE id_carrera = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function editarCarrera($id, $nombre, $clave) {
        $stmt = $this->conn->prepare("UPDATE carreras SET nombre = ?, clave = ? WHERE id_carrera = ?");
        $stmt->bind_param("ssi", $nombre, $clave, $id);
        return $stmt->execute();
    }

    public function eliminarCarrera($id) {
        $stmt = $this->conn->prepare("DELETE FROM carreras WHERE id_carrera = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
