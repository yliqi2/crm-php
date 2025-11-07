<?php

require_once __DIR__ . '/../model/db.php';

class UserController {

    public function getClientesForOwner() {

        $id_usuario = (int) $_SESSION['id_usuario'];

        $db = new DB();
        $conexion = $db->getConnection();

        $stmt = $conexion->prepare("SELECT * FROM cliente WHERE usuario_responsable = ?");
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('i', $id_usuario);
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


    public function getClienteIfOwner($id_cliente) {

        $id_usuario = (int) $_SESSION['id_usuario'];
        $id_cliente = (int) $id_cliente;

        $db = new DB();
        $conexion = $db->getConnection();

        $stmt = $conexion->prepare("SELECT * FROM cliente WHERE id_cliente = ? AND usuario_responsable = ? LIMIT 1");
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('ii', $id_cliente, $id_usuario);
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


    public function modifyCliente($id_cliente, $nombre_completo, $email, $tlf, $empresa) {
        $db = new DB();
        $conexion = $db->getConnection();

        $stmt = $conexion->prepare("UPDATE cliente SET nombre_completo = ?, email = ?, tlf = ?, empresa = ? WHERE id_cliente = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('ssssi', $nombre_completo, $email, $tlf, $empresa, $id_cliente);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function searchClientesByName($name) {

        $id_usuario = (int) $_SESSION['id_usuario'];
        $name_like = '%' . $name . '%';

        $db = new DB();
        $conexion = $db->getConnection();

        $stmt = $conexion->prepare("SELECT * FROM cliente WHERE usuario_responsable = ? AND nombre_completo LIKE ?");
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('is', $id_usuario, $name_like);
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

    public function searchClientesByEmpresa($empresa) {

        $id_usuario = (int) $_SESSION['id_usuario'];
        $empresa_like = '%' . $empresa . '%';

        $db = new DB();
        $conexion = $db->getConnection();

        $stmt = $conexion->prepare("SELECT * FROM cliente WHERE usuario_responsable = ? AND empresa LIKE ?");
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('is', $id_usuario, $empresa_like);
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
