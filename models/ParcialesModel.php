<?php
class ParcialesModel {
    private $conn;

    public function __construct($conexion){
        $this->conn = $conexion;
    }

    public function getParciales(){
        $sql = "SELECT * FROM parciales ORDER BY numero_parcial ASC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function agregarParcial($numero, $fecha_inicio, $fecha_fin){
        $stmt = $this->conn->prepare("INSERT INTO parciales (numero_parcial, fecha_inicio, fecha_fin) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $numero, $fecha_inicio, $fecha_fin);
        return $stmt->execute();
    }

    public function editarParcial($id, $numero, $fecha_inicio, $fecha_fin){
        $stmt = $this->conn->prepare("UPDATE parciales SET numero_parcial=?, fecha_inicio=?, fecha_fin=? WHERE id_parcial=?");
        $stmt->bind_param("issi", $numero, $fecha_inicio, $fecha_fin, $id);
        return $stmt->execute();
    }

    public function eliminarParcial($id){
        $stmt = $this->conn->prepare("DELETE FROM parciales WHERE id_parcial=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
