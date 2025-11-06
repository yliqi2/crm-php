<?php

require_once __DIR__ . '/../model/db.php';

class UserController {


    /**
     * Obtiene todos los clientes asignados al usuario en sesión.
     * Retorna un array (posiblemente vacío) de arrays asociativos o null si no hay sesión.
     *
     * @return array|null
     */
    public function getClientesForOwner() {

        if (!isset($_SESSION['id_usuario'])) {
            return null; // no hay sesión
        }

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

}
