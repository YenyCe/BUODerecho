<?php
class CargaAcademicaModel {

    private $conn;

    public function __construct($conexion){
        $this->conn = $conexion;
    }

    /* ================= CONFIGURACIÓN ================= */

    // Obtener configuración activa por carrera
    public function getConfigByCarrera($id_carrera){
        $stmt = $this->conn->prepare("
            SELECT *
            FROM carga_academica
            WHERE id_carrera = ?
            ORDER BY id_carga DESC
            LIMIT 1
        ");
        $stmt->bind_param("i", $id_carrera);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Crear configuración
    public function crearConfig($data){
        $stmt = $this->conn->prepare("
            INSERT INTO carga_academica
            (id_carrera, clave_oficio, texto_presentacion, ciclo_escolar,
             claustro_texto, texto_pie, nombre_director, cargo_director, archivo_firma)
            VALUES (?,?,?,?,?,?,?,?,?)
        ");
        $stmt->bind_param(
            "issssssss",
            $data['id_carrera'],
            $data['clave_oficio'],
            $data['texto_presentacion'],
            $data['ciclo_escolar'],
            $data['claustro_texto'],
            $data['texto_pie'],
            $data['nombre_director'],
            $data['cargo_director'],
            $data['archivo_firma']
        );
        return $stmt->execute();
    }

    // Editar configuración
    public function editarConfig($id_carga, $data){
        $stmt = $this->conn->prepare("
            UPDATE carga_academica SET
                clave_oficio=?,
                texto_presentacion=?,
                ciclo_escolar=?,
                claustro_texto=?,
                texto_pie=?,
                nombre_director=?,
                cargo_director=?,
                archivo_firma=?
            WHERE id_carga=?
        ");
        $stmt->bind_param(
            "ssssssssi",
            $data['clave_oficio'],
            $data['texto_presentacion'],
            $data['ciclo_escolar'],
            $data['claustro_texto'],
            $data['texto_pie'],
            $data['nombre_director'],
            $data['cargo_director'],
            $data['archivo_firma'],
            $id_carga
        );
        return $stmt->execute();
    }
}
