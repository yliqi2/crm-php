<?php

require_once __DIR__ . '/../model/db.php';
require_once __DIR__ . '/../model/cliente.php';

class ClientController {

    public function crearClientes($res) {
        $clientes = [];
        if ($res && $res->num_rows > 1) {
            while ($r = $res->fetch_assoc()) {
                $cliente = new Cliente(
                    $r['id_cliente'],
                    $r['nombre_completo'],
                    $r['email'],
                    $r['tlf'],
                    $r['empresa'],
                    $r['fecha_registro'],
                    $r['usuario_responsable']
                 );

                $clientes[] = $cliente;

            }
            return $clientes;
        } else {
            $r = $res->fetch_assoc();
            $client = new Cliente(
                $r['id_cliente'],
                $r['nombre_completo'],
                $r['email'],
                $r['tlf'],
                $r['empresa'],
                $r['fecha_registro'],
                $r['usuario_responsable']
            );
            return $client;
        }
    }

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
        $stmt->close();
        return $this->crearClientes($res);
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
            $stmt->close();
            return $this->crearClientes($res);
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
        $stmt->close();
        return $this->crearClientes($res);
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
        $stmt->close();
        return $this->crearClientes($res);
    }

    

}
