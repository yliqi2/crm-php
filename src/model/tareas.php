<?php

class Tareas {
    private $id_tarea;
    private $id_oportunidad;
    private $descripcion;
    private $fecha;
    private $estado;

    public function __construct($id_tarea = null, $id_oportunidad = null, $descripcion = null, $fecha = null, $estado = 'pendiente') {
        $this->id_tarea = $id_tarea;
        $this->id_oportunidad = $id_oportunidad;
        $this->descripcion = $descripcion;
        $this->fecha = $fecha;
        $this->estado = $estado;
    }

    // Getters
    public function getIdTarea() {
        return $this->id_tarea;
    }

    public function getIdOportunidad() {
        return $this->id_oportunidad;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function getFecha() {
        return $this->fecha;
    }

    public function getEstado() {
        return $this->estado;
    }

    // Setters
    public function setIdTarea($id_tarea) {
        $this->id_tarea = $id_tarea;
    }

    public function setIdOportunidad($id_oportunidad) {
        $this->id_oportunidad = $id_oportunidad;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    public function setEstado($estado) {
        // Validar que el estado sea 'pendiente' o 'completada'
        if ($estado === 'pendiente' || $estado === 'completada') {
            $this->estado = $estado;
        }
    }

    // Métodos auxiliares
    public function isPendiente() {
        return $this->estado === 'pendiente';
    }

    public function isCompletada() {
        return $this->estado === 'completada';
    }

    public function completar() {
        $this->estado = 'completada';
    }

    public function marcarPendiente() {
        $this->estado = 'pendiente';
    }
}

?>