<?php

require_once __DIR__ . '/../model/tarea.php';
require_once __DIR__ . '/../model/db.php';
require_once __DIR__ . '/../controller/usuario_controller.php';
require_once __DIR__ . '/../controller/oportunity_controller.php';

class TareasController {

    public function __construct() {
        $this->db = new DB();
        $this->usuarioController = new UsuarioController();
        $this->oportunityController = new OportunityController();
    }
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
    }

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
}


?>