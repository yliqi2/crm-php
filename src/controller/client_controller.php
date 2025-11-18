<?php

require_once __DIR__ . '/../model/db.php';
require_once __DIR__ . '/../model/cliente.php';
require_once __DIR__ . '/../controller/usuario_controller.php';

class ClientController {

    public function __construct() {
        $this->db = new DB();
        $this->usuarioController = new UsuarioController();
    }

    //Funcion para convertir a objetos Cliente
    public function crearClientes($res) {
        $clientes = [];
        if ($res && $res->num_rows > 0) {
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
        } 
    }

    // funcion comprobación de correo si esta en uso, además de comprobar si es el correo es del propio usuario para poder hacer el update
    public function emailAlreadyInUse($email, $excludeId = null) {
        $conexion = $this->db->getConnection();

        if ($excludeId === null) {
            $stmt = $conexion->prepare("SELECT 1 FROM cliente WHERE email = ? LIMIT 1");
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param("s", $email);
        } else {
            $stmt = $conexion->prepare("SELECT 1 FROM cliente WHERE email = ? AND id_cliente != ? LIMIT 1");
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param("si", $email, $excludeId);
        }

        $stmt->execute();
        $res = $stmt->get_result();
        $exists = ($res && $res->num_rows > 0);
        $stmt->close();
        return $exists;
    }

    // Actualiza el cliente
    public function modifyCliente($id_cliente, $nombre_completo, $email, $tlf, $empresa) {
        $conexion = $this->db->getConnection();

        $stmt = $conexion->prepare("UPDATE cliente SET nombre_completo = ?, email = ?, tlf = ?, empresa = ? WHERE id_cliente = ?");
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('ssssi', $nombre_completo, $email, $tlf, $empresa, $id_cliente);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
  
    // Devuelve todos los clientes del usuario logueado
    public function getClientesForOwner() {
        $id_usuario = (int) $_SESSION['id_usuario'];
        $conexion = $this->db->getConnection();
        // If the user is admin, return ALL clients; otherwise only those the user is responsible for
        if ($this->usuarioController->isAdmin($id_usuario)) {
            $stmt = $conexion->prepare("SELECT * FROM cliente");
            if (!$stmt) {
                return [];
            }
            $stmt->execute();
        } else {
            $stmt = $conexion->prepare("SELECT * FROM cliente WHERE usuario_responsable = ?");
            if (!$stmt) {
                return [];
            }
            $stmt->bind_param('i', $id_usuario);
            $stmt->execute();
        }

        $res = $stmt->get_result();
        $stmt->close();
        return $this->crearClientes($res);
    }

    // Devuelve la información para modificar el cliente solo si es admin
    public function getClienteIfOwner($id_cliente) {

        $id_usuario = (int) $_SESSION['id_usuario'];
        $id_cliente = (int) $id_cliente;

        $conexion = $this->db->getConnection();

        if ($this->usuarioController->isAdmin($id_usuario)) {
            $stmt = $conexion->prepare("SELECT * FROM cliente WHERE id_cliente = ? LIMIT 1");
            $stmt->bind_param('i', $id_cliente);
            $stmt->execute();
        } 

        if (!$stmt) {
            return null;
        }

        $res = $stmt->get_result();
        if ($res && $res->num_rows === 1) {
            $stmt->close();
            return $this->crearClientes($res)[0] ?? null;
        }
        $stmt->close();
        return null;
    }

    // Busca clientes por nombre
    public function searchClientesByName($name) {

        $id_usuario = (int) $_SESSION['id_usuario'];
        $name_like = '%' . $name . '%';

        $conexion = $this->db->getConnection();

        if ($this->usuarioController->isAdmin($id_usuario)) {
            $stmt = $conexion->prepare("SELECT * FROM cliente WHERE nombre_completo LIKE ?");
            if (!$stmt) {
                return [];
            }
            $stmt->bind_param('s', $name_like);
            $stmt->execute();
        } else {
            $stmt = $conexion->prepare("SELECT * FROM cliente WHERE usuario_responsable = ? AND nombre_completo LIKE ?");
            if (!$stmt) {
                return [];
            }
            $stmt->bind_param('is', $id_usuario, $name_like);
            $stmt->execute();
        }

        $res = $stmt->get_result();
        $stmt->close();
        return $this->crearClientes($res);
    }


    // Busca clientes por empresa
    public function searchClientesByEmpresa($empresa) {

        $id_usuario = (int) $_SESSION['id_usuario'];
        $empresa_like = '%' . $empresa . '%';
        
        $conexion = $this->db->getConnection();

        if ($this->usuarioController->isAdmin($id_usuario)) {
            $stmt = $conexion->prepare("SELECT * FROM cliente WHERE empresa LIKE ?");
            if (!$stmt) {
                return [];
            }
            $stmt->bind_param('s', $empresa_like);
            $stmt->execute();
        } else {
            $stmt = $conexion->prepare("SELECT * FROM cliente WHERE usuario_responsable = ? AND empresa LIKE ?");
            if (!$stmt) {
                return [];
            }
            $stmt->bind_param('is', $id_usuario, $empresa_like);
            $stmt->execute();
        }

        $res = $stmt->get_result();
        $stmt->close();
        return $this->crearClientes($res);
    }

    //eliminar cliente
    public function removeCliente($id_cliente) {
        $conexion = $this->db->getConnection();

        $stmt = $conexion->prepare("DELETE FROM cliente WHERE id_cliente = ?");
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('i', $id_cliente);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

}