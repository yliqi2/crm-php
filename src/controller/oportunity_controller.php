<?php

require_once __DIR__ . '/../model/db.php';
require_once __DIR__ . '/../model/oportunidad.php';
require_once __DIR__ . '/../controller/usuario_controller.php';

class OportunityController {

    public function __construct() {
        $this->db = new DB();
        $this->usuarioController = new UsuarioController();
    }


    //Funcion para crear objetos Oportunidad
    public function crearOportunidades($res) {
        $oportunidades = [];
        if ($res && $res->num_rows > 0) {
            while ($r = $res->fetch_assoc()) {
                $oportunidad = new Oportunidad(
                    $r['id_oportunidad'],
                    $r['id_cliente'],
                    $r['titulo'],
                    $r['descripcion'],
                    $r['valor_estimado'],
                    $r['estado'],
                    $r['f_creacion'],
                    $r['usuario_responsable']
                 );

                $oportunidades[] = $oportunidad;

            }
        }
        return $oportunidades;
    }
    
    // Funcion para crear una nueva oportunidad
    public function insertOportunity($id_cliente, $titulo, $descripcion, $valor_estimado) {
        $id_usuario = (int) $_SESSION['id_usuario'];
        $id_cliente = (int) $id_cliente;
        $conexion = $this->db->getConnection();

        // Preferir el usuario_responsable del cliente (si existe) para la nueva oportunidad.
        // Esto evita que un admin que crea la oportunidad quede como responsable.
        $usuario_responsable = $id_usuario; // fallback
        $respStmt = $conexion->prepare("SELECT usuario_responsable FROM cliente WHERE id_cliente = ? LIMIT 1");
        if ($respStmt) {
            $respStmt->bind_param('i', $id_cliente);
            $respStmt->execute();
            $res = $respStmt->get_result();
            if ($res && $res->num_rows === 1) {
                $row = $res->fetch_assoc();
                if (!empty($row['usuario_responsable'])) {
                    $usuario_responsable = (int)$row['usuario_responsable'];
                }
            }
            $respStmt->close();
        }

        $stmt = $conexion->prepare("INSERT INTO oportunidad (id_cliente, titulo, descripcion, valor_estimado, usuario_responsable) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            return false;
        }
        // types: int, string, string, double, int
        $stmt->bind_param('issdi', $id_cliente, $titulo, $descripcion, $valor_estimado, $usuario_responsable);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
    
    // Funncion para obtener las oportunidades de un cliente
    public function getOportunidadesByCliente($id_cliente) {

        $id_usuario = (int) $_SESSION['id_usuario'];
        $id_cliente = (int) $id_cliente;
        
        $conexion = $this->db->getConnection();

        if ($this->usuarioController->isAdmin($id_usuario)) {
            $stmt = $conexion->prepare("SELECT o.* FROM oportunidad o JOIN cliente c ON o.id_cliente = c.id_cliente WHERE o.id_cliente = ?");
            $stmt->bind_param('i', $id_cliente);
        } else {
            $stmt = $conexion->prepare("SELECT o.* FROM oportunidad o JOIN cliente c ON o.id_cliente = c.id_cliente WHERE o.id_cliente = ? AND c.usuario_responsable = ?");
            $stmt->bind_param('ii', $id_cliente, $id_usuario);
        }

       
        if (!$stmt) {
            return [];
        }

        $stmt->execute();
        $res = $stmt->get_result();
        $stmt->close();
        return $this->crearOportunidades($res);
    }



    // Funcion para obtener una oportunidad por su ID
    public function getOportunidadById($id_oportunidad) {

        $id_usuario = (int) $_SESSION['id_usuario'];
        $id_oportunidad = (int) $id_oportunidad;

        $conexion = $this->db->getConnection();

        if ($this->usuarioController->isAdmin($id_usuario)) {
            $stmt = $conexion->prepare("SELECT * FROM oportunidad WHERE id_oportunidad = ? LIMIT 1");
            $stmt->bind_param('i', $id_oportunidad);
        } else {

            $stmt = $conexion->prepare("SELECT o.* FROM oportunidad o JOIN cliente c ON o.id_cliente = c.id_cliente WHERE o.id_oportunidad = ? AND c.usuario_responsable = ? LIMIT 1");
            $stmt->bind_param('ii', $id_oportunidad, $id_usuario);

        }
        if (!$stmt) {
            return null;
        }

        $stmt->execute();
        $res = $stmt->get_result();

        $stmt->close();
        return $this->crearOportunidades($res)[0] ?? null;
    }

    // Funcion para actualizar una oportunidad
    public function updateOportunidad($id_oportunidad, $titulo, $descripcion, $valor_estimado, $estado) {
        $id_usuario = (int) $_SESSION['id_usuario'];
        $id_oportunidad = (int) $id_oportunidad;

        $conexion = $this->db->getConnection();

        if ($this->usuarioController->isAdmin($id_usuario)) {
            $stmt = $conexion->prepare("UPDATE oportunidad SET titulo = ?, descripcion = ?, valor_estimado = ?, estado = ? WHERE id_oportunidad = ?");
            if (!$stmt) {
                return false;
            }
            // types: string, string, double, string (estado), int(id_oportunidad)
            $stmt->bind_param('ssdsi', $titulo, $descripcion, $valor_estimado, $estado, $id_oportunidad);


        } else {
            $stmt = $conexion->prepare("UPDATE oportunidad SET titulo = ?, descripcion = ?, valor_estimado = ?, estado = ? WHERE id_oportunidad = ? AND usuario_responsable = ?");
            if (!$stmt) {
                return false;
            }
            // types: string, string, double, string (estado), int(id_oportunidad), int(id_usuario)
            $stmt->bind_param('ssdsii', $titulo, $descripcion, $valor_estimado, $estado, $id_oportunidad, $id_usuario);
        }

        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    // Funcion para eliminar una oportunidad
    public function deleteOportunidad($id_oportunidad) {
        $id_usuario = (int) $_SESSION['id_usuario'];
        $id_oportunidad = (int) $id_oportunidad;

        $conexion = $this->db->getConnection();

        if ($this->usuarioController->isAdmin($id_usuario)) {
            
            $stmt = $conexion->prepare("DELETE FROM oportunidad WHERE id_oportunidad = ?");
            $stmt->bind_param('i', $id_oportunidad);

        } else {
        
            $stmt = $conexion->prepare("DELETE o FROM oportunidad o JOIN cliente c ON o.id_cliente = c.id_cliente WHERE o.id_oportunidad = ? AND c.usuario_responsable = ?");
            $stmt->bind_param('ii', $id_oportunidad, $id_usuario);

        }

        if (!$stmt) {
            return false;
        }
        
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

}


?>