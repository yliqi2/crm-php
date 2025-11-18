<?php

require_once __DIR__ . '/../model/tareas.php';
require_once __DIR__ . '/../model/db.php';
require_once __DIR__ . '/../controller/usuario_controller.php';
require_once __DIR__ . '/../controller/oportunity_controller.php';

class TareasController {

    public function __construct() {
        $this->db = new DB();
        $this->usuarioController = new UsuarioController();
        $this->oportunityController = new OportunityController();
    }

    //crea los objetos de tareas
    public function crearTareas($res) {
        $tareas = [];
        if ($res && $res->num_rows > 0) {
            while ($r = $res->fetch_assoc()) {
                $tarea = new Tareas(
                    $r['id_tarea'] ?? null,
                    $r['id_oportunidad'] ?? null,
                    $r['descripcion'] ?? null,
                    $r['fecha'] ?? null,
                    $r['estado'] ?? 'pendiente'
                );
                $tareas[] = $tarea;
            }
        }
        return $tareas;
    }

    //hace el insert a la base de datos cuando se crea una tarea en el creartareas
    public function insertTarea($id_oportunidad, $descripcion, $estado = 'pendiente') {
        $id_oportunidad = (int) $id_oportunidad;
        $id_usuario = isset($_SESSION['id_usuario']) ? (int)$_SESSION['id_usuario'] : null;

        if ($id_usuario === null) {
            return false;
        }
        // Validar existencia de oportunidad y permisos
        $oportunidad = $this->oportunityController->getOportunidadById($id_oportunidad);
        if (!$oportunidad) {
            return false;
        }

        if (!$this->usuarioController->isAdmin($id_usuario) && $oportunidad->getUsuarioResponsable() !== $id_usuario) {
            return false;
        }

        // Si el usuario no es admin, ignorar el estado enviado y forzar 'pendiente'.
        $isAdmin = $this->usuarioController->isAdmin($id_usuario);
        if ($isAdmin) {
            // Normalizar estado solo si es admin
            $estado = ($estado === 'completada') ? 'completada' : 'pendiente';
        } else {
            $estado = 'pendiente';
        }

        $conexion = $this->db->getConnection();
        $stmt = $conexion->prepare("INSERT INTO tareas (id_oportunidad, descripcion, estado) VALUES (?, ?, ?)");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('iss', $id_oportunidad, $descripcion, $estado);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    //devuelve las tareas de una oportunidad
    public function getTareasByOportunidad($id_oportunidad) {
        $id_oportunidad = (int) $id_oportunidad;
        $id_usuario = isset($_SESSION['id_usuario']) ? (int)$_SESSION['id_usuario'] : null;

        // Obtener la oportunidad para validar permisos
        $oportunidad = $this->oportunityController->getOportunidadById($id_oportunidad);
        if (!$oportunidad) {
            return [];
        }

        // Si no es admin y no es responsable, no puede ver
        if ($id_usuario === null || (!$this->usuarioController->isAdmin($id_usuario) && $oportunidad->getUsuarioResponsable() !== $id_usuario)) {
            return [];
        }

        $conexion = $this->db->getConnection();
        $stmt = $conexion->prepare("SELECT * FROM tareas WHERE id_oportunidad = ? ORDER BY fecha ASC");
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('i', $id_oportunidad);
        $stmt->execute();
        $res = $stmt->get_result();
        $stmt->close();


        return $this->crearTareas($res);
    }
    //funcion para actualizar el estado de la tarea a completada
    public function completarTarea($id_tarea) {
        $id_tarea = (int) $id_tarea;
        $id_usuario = isset($_SESSION['id_usuario']) ? (int)$_SESSION['id_usuario'] : null;

        if ($id_usuario === null) {
            return false;
        }

        // Obtener la tarea para validar permisos
        $conexion = $this->db->getConnection();
        $stmt = $conexion->prepare("SELECT t.*, o.usuario_responsable FROM tareas t JOIN oportunidad o ON t.id_oportunidad = o.id_oportunidad WHERE t.id_tarea = ? LIMIT 1");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('i', $id_tarea);
        $stmt->execute();
        $res = $stmt->get_result();
        $stmt->close();

        if ($res && $res->num_rows === 1) {
            $row = $res->fetch_assoc();

            $stmtUpdate = $conexion->prepare("UPDATE tareas SET estado = 'completada' WHERE id_tarea = ?");
            if (!$stmtUpdate) {
                return false;
            }
            $stmtUpdate->bind_param('i', $id_tarea);
            $success = $stmtUpdate->execute();
            $stmtUpdate->close();
            return $success;
        }

        return false;
    }

}


?>