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

}


?>