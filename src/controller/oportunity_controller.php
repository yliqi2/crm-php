<?php

require_once __DIR__ . '/../model/db.php';

class OportunityController {
    
    public function getOportunidadesByCliente($id_cliente) {

        $id_usuario = (int) $_SESSION['id_usuario'];
        $id_cliente = (int) $id_cliente;

        $db = new DB();
        $conexion = $db->getConnection();

        $stmt = $conexion->prepare("SELECT o.* FROM oportunidad o JOIN cliente c ON o.id_cliente = c.id_cliente WHERE o.id_cliente = ? AND c.usuario_responsable = ?");
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('ii', $id_cliente, $id_usuario);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
            }
        }
        $stmt->close();
        return $rows;
    }

    public function createOportunidad($id_cliente, $titulo, $descripcion, $valor_estimado) {
        $id_usuario = (int) $_SESSION['id_usuario'];
        $id_cliente = (int) $id_cliente;

        $db = new DB();
        $conexion = $db->getConnection();

        $stmt = $conexion->prepare("INSERT INTO oportunidad (id_cliente, titulo, descripcion, valor_estimado, usuario_responsable) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('isssi', $id_cliente, $titulo, $descripcion, $valor_estimado, $id_usuario);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    //actualizar oportunidad y seleccionar por la oportunidad

    public function getOportunidadById($id_oportunidad) {

        $id_usuario = (int) $_SESSION['id_usuario'];
        $id_oportunidad = (int) $id_oportunidad;

        $db = new DB();
        $conexion = $db->getConnection();

        $stmt = $conexion->prepare("SELECT o.* FROM oportunidad o JOIN cliente c ON o.id_cliente = c.id_cliente WHERE o.id_oportunidad = ? AND c.usuario_responsable = ? LIMIT 1");
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('ii', $id_oportunidad, $id_usuario);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows === 1) {
            $row = $res->fetch_assoc();
            $stmt->close();
            return $row;
        }
        $stmt->close();
        return null;
    }

    public function updateOportunidad($id_oportunidad, $titulo, $descripcion, $valor_estimado, $estado) {
        $id_usuario = (int) $_SESSION['id_usuario'];
        $id_oportunidad = (int) $id_oportunidad;

        $db = new DB();
        $conexion = $db->getConnection();

        $stmt = $conexion->prepare("UPDATE oportunidad o JOIN cliente c ON o.id_cliente = c.id_cliente SET o.titulo = ?, o.descripcion = ?, o.valor_estimado = ?, o.estado = ? WHERE o.id_oportunidad = ? AND c.usuario_responsable = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('ssdiii', $titulo, $descripcion, $valor_estimado, $estado, $id_oportunidad, $id_usuario);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

}


?>