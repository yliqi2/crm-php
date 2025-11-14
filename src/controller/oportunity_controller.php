<?php

require_once __DIR__ . '/../model/db.php';
require_once __DIR__ . '/../model/oportunidad.php';

class OportunityController {

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
    
    // Funncion para obtener las oportunidades de un cliente
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
        $stmt->close();
        return $this->crearOportunidades($res);
    }



    // Funcion para obtener una oportunidad por su ID
    public function getOportunidadById($id_oportunidad) {

        $id_usuario = (int) $_SESSION['id_usuario'];
        $id_oportunidad = (int) $id_oportunidad;

        $db = new DB();
        $conexion = $db->getConnection();

        $stmt = $conexion->prepare("SELECT o.* FROM oportunidad o JOIN cliente c ON o.id_cliente = c.id_cliente WHERE o.id_oportunidad = ? AND c.usuario_responsable = ? LIMIT 1");
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('ii', $id_oportunidad, $id_usuario);
        $stmt->execute();
        $res = $stmt->get_result();

        $stmt->close();
        return $this->crearOportunidades($res)[0] ?? null;
    }

    // Funcion para actualizar una oportunidad
    public function updateOportunidad($id_oportunidad, $titulo, $descripcion, $valor_estimado, $estado) {
        $id_usuario = (int) $_SESSION['id_usuario'];
        $id_oportunidad = (int) $id_oportunidad;

        $db = new DB();
        $conexion = $db->getConnection();

        $stmt = $conexion->prepare("UPDATE oportunidad o JOIN cliente c ON o.id_cliente = c.id_cliente SET o.titulo = ?, o.descripcion = ?, o.valor_estimado = ?, o.estado = ? WHERE o.id_oportunidad = ? AND c.usuario_responsable = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('ssdiii', $titulo, $descripcion, $valor_estimado, $estado, $id_oportunidad, $id_usuario);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

}


?>