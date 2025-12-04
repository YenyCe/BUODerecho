<?php
class ParcialesModel {

    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    public function getParciales($id_carrera = null) {
        if ($id_carrera) {
            $sql = "SELECT * FROM parciales WHERE id_carrera = $id_carrera ORDER BY numero_parcial ASC";
        } else {
            $sql = "SELECT p.*, c.nombre AS carrera 
                    FROM parciales p
                    JOIN carreras c ON c.id_carrera = p.id_carrera
                    ORDER BY p.id_carrera, p.numero_parcial ASC";
        }
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function agregarParcial($numero, $inicio, $fin, $id_carrera) {
        $stmt = $this->conn->prepare("INSERT INTO parciales (numero_parcial, fecha_inicio, fecha_fin, id_carrera) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issi", $numero, $inicio, $fin, $id_carrera);
        return $stmt->execute();
    }

    public function editarParcial($id, $numero, $inicio, $fin) {
        $stmt = $this->conn->prepare("UPDATE parciales SET numero_parcial=?, fecha_inicio=?, fecha_fin=? WHERE id_parcial=?");
        $stmt->bind_param("issi", $numero, $inicio, $fin, $id);
        return $stmt->execute();
    }

    public function eliminarParcial($id) {
        return $this->conn->query("DELETE FROM parciales WHERE id_parcial=$id");
    }
}
?>
