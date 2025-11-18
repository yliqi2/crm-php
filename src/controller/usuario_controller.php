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

    // es admin?
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

    // funcion que devuelve usuarios
    public function getAllUsers() {
        $conexion = $this->db->getConnection();
        // comprobar que el usuario actual es admin (usar la sesión si está disponible)
        $currentUserId = isset($_SESSION['id_usuario']) ? (int)$_SESSION['id_usuario'] : null;
        if ($currentUserId !== null && $this->isAdmin($currentUserId)) {
            $stmt = $conexion->prepare("SELECT * FROM usuario");
        if (!$stmt) {
            return [];
        }
        $stmt->execute();
        $res = $stmt->get_result();
        $stmt->close();

        $usuarios = [];
        while ($row = $res->fetch_assoc()) {
            $usuario = new Usuario(
                $row['id_usuario'],
                $row['nombre_completo'],
                $row['email'],
                null, 
                isset($row['role']) ? $row['role'] : 'vendedor'
            );
            $usuarios[] = $usuario;
        }
        return $usuarios;
        }

        return [];
    }

    // funcion para eliminar usuario x id
    public function deleteUser($id_usuario) {
        $conexion = $this->db->getConnection();
        $currentUserId = isset($_SESSION['id_usuario']) ? (int)$_SESSION['id_usuario'] : null;
        if ($currentUserId !== null && $this->isAdmin($currentUserId)) {
            $stmt = $conexion->prepare("DELETE FROM usuario WHERE id_usuario = ?");
            $stmt->bind_param('i', $id_usuario);
        }
        if (!$stmt) {
            return false;
        }

        $ok = $stmt->execute();
        
        if ($ok) {
            return true;
        }
        $stmt->close();

        return false;
    }

    public function createUser($nombre_completo, $email, $contra, $role) {
        $conexion = $this->db->getConnection();

        // Verificar si ya existe un usuario con ese email
        if ($this->userExists($email)) {
            // Email ya registrado
            return null;
        }

        // Hashear la contraseña
        $contraHasheada = password_hash($contra, PASSWORD_DEFAULT);

        // Insertar usuario (la columna `role` en la BD tiene default 'vendedor')
        $stmt = $conexion->prepare("INSERT INTO usuario (nombre_completo, email, contra, role) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param("ssss", $nombre_completo, $email, $contraHasheada, $role);
        $ok = $stmt->execute();
        if ($ok) {
            $id = $conexion->insert_id;
            // No devolver el hash de la contraseña en el objeto
            // DB default for role is 'vendedor', so set that in the returned object
            return new Usuario($id, $nombre_completo, $email, null, $role);
        }

        return null;
    }


    public function getUserById($id_usuario) {
        $conexion = $this->db->getConnection();
        $stmt = $conexion->prepare("SELECT * FROM usuario WHERE id_usuario = ? LIMIT 1");
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('i', $id_usuario);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows === 1) {
            $row = $res->fetch_assoc();
            $stmt->close();
            return new Usuario(
                $row['id_usuario'],
                $row['nombre_completo'],
                $row['email'],
                null,
                isset($row['role']) ? $row['role'] : 'vendedor'
            );
        }
        $stmt->close();
        return null;
    }

    public function updateUser($id_usuario, $nombre_completo, $email, $role, $contra = null) {
        $conexion = $this->db->getConnection();

        $currentUserId = isset($_SESSION['id_usuario']) ? (int)$_SESSION['id_usuario'] : null;
        if ($currentUserId === null) {
            return false;
        }

        // Permitir si es admin o está editando su propia cuenta
        if (!$this->isAdmin($currentUserId) && $currentUserId !== (int)$id_usuario) {
            return false;
        }

        // Comprobar email duplicado en otro usuario
        $check = $conexion->prepare("SELECT id_usuario FROM usuario WHERE email = ? LIMIT 1");
        if (!$check) {
            return false;
        }
        $check->bind_param('s', $email);
        $check->execute();
        $res = $check->get_result();
        if ($res && $res->num_rows === 1) {
            $row = $res->fetch_assoc();
            if ((int)$row['id_usuario'] !== (int)$id_usuario) {
                $check->close();
                return false; // email en uso por otro
            }
        }
        $check->close();

        $role = ($role === 'admin') ? 'admin' : 'vendedor';

        // Construir consulta según si se cambia contraseña
        if ($contra !== null && $contra !== '') {
            $contraHasheada = password_hash($contra, PASSWORD_DEFAULT);
            $stmt = $conexion->prepare("UPDATE usuario SET nombre_completo = ?, email = ?, contra = ?, role = ? WHERE id_usuario = ?");
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param('ssssi', $nombre_completo, $email, $contraHasheada, $role, $id_usuario);
        } else {
            $stmt = $conexion->prepare("UPDATE usuario SET nombre_completo = ?, email = ?, role = ? WHERE id_usuario = ?");
            if (!$stmt) {
                return false;
            }
            $stmt->bind_param('sssi', $nombre_completo, $email, $role, $id_usuario);
        }

        $ok = $stmt->execute();
        if ($ok) {
            $affected = $stmt->affected_rows;
            $stmt->close();
            return ($affected >= 0);
        }
        $stmt->close();
        return false;
    }

}
?>
