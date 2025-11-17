<?php

require_once __DIR__ . '/../model/usuario.php';
require_once __DIR__ . '/../model/db.php';

class UsuarioController {
    private $db;

    public function __construct() {
        $this->db = new DB();
    }

    // Comprueba si ya existe un usuario con el email dado
    private function userExists($email) {
        $conexion = $this->db->getConnection();
        $stmt = $conexion->prepare("SELECT 1 FROM usuario WHERE email = ? LIMIT 1");
        if (!$stmt) {
            return false; 
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $exists = ($res && $res->num_rows > 0);
        $stmt->close();
        return $exists;
    }

    public function login($email, $contra) {
        $conexion = $this->db->getConnection();
        $stmt = $conexion->prepare("SELECT * FROM usuario WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            // DB now stores role enum ('admin'|'vendedor') in column `role`
            $usuario = new Usuario(
                $row['id_usuario'],
                $row['nombre_completo'],
                $row['email'],
                $row['contra'],
                isset($row['role']) ? $row['role'] : 'vendedor'
            );

            if (password_verify($contra, $usuario->getContra())) {
                return $usuario;
            }
        }
        return null;
    }

    public function register($nombre_completo, $email, $contra) {
        $conexion = $this->db->getConnection();

        // Verificar si ya existe un usuario con ese email
        if ($this->userExists($email)) {
            // Email ya registrado
            return null;
        }

        // Hashear la contraseña
        $contraHasheada = password_hash($contra, PASSWORD_DEFAULT);

        // Insertar usuario (la columna `role` en la BD tiene default 'vendedor')
        $stmt = $conexion->prepare("INSERT INTO usuario (nombre_completo, email, contra) VALUES (?, ?, ?)");
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param("sss", $nombre_completo, $email, $contraHasheada);
        $ok = $stmt->execute();
        if ($ok) {
            $id = $conexion->insert_id;
            // No devolver el hash de la contraseña en el objeto
            // DB default for role is 'vendedor', so set that in the returned object
            return new Usuario($id, $nombre_completo, $email, null, 'vendedor');
        }

        return null;
    }

    public function isAdmin($id_usuario) {
        $conexion = $this->db->getConnection();
        $stmt = $conexion->prepare("SELECT role FROM usuario WHERE id_usuario = ? LIMIT 1");
        $stmt->bind_param('i', $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            return $row['role'] === 'admin';
        }
        return false;
    }

}

?>
