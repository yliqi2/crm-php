<?php

class Oportunidad {
    private $id_oportunidad; //primary key
    private $id_cliente; //foreign key de cliente
    private $titulo; //varchar 100
    private $descripcion; //varchar 250
    private $valor; //decimal 10.2
    private $estado; //enum [progreso, gananada, perdida]
    private $f_creacion; //datetime
    private $usuario_responsable; //id_usuario

    public function __construct($id_oportunidad, $id_cliente, $titulo, $descripcion, $valor, $estado, $f_creacion, $usuario_responsable) {
        $this->id_oportunidad = $id_oportunidad;
        $this->id_cliente = $id_cliente;
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
        $this->valor = $valor;
        $this->estado = $estado;
        $this->f_creacion = $f_creacion;
        $this->usuario_responsable = $usuario_responsable;
    }

    // Getters
    public function getIdOportunidad() {
        return $this->id_oportunidad;
    }

    public function getIdCliente() {
        return $this->id_cliente;
    }

    public function getTitulo() {
        return $this->titulo;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function getValor() {
        return $this->valor;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function getFCreacion() {
        return $this->f_creacion;
    }

    public function getUsuarioResponsable() {
        return $this->usuario_responsable;
    }

    // Setters
    public function setIdOportunidad($id_oportunidad) {
        $this->id_oportunidad = $id_oportunidad;
    }

    public function setIdCliente($id_cliente) {
        $this->id_cliente = $id_cliente;
    }

    public function setTitulo($titulo) {
        $this->titulo = $titulo;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function setValor($valor) {
        $this->valor = $valor;
    }

    public function setEstado($estado) {
        // Validar que el estado sea uno de: 'progreso', 'gananada', 'perdida'
        if ($estado === 'progreso' || $estado === 'gananada' || $estado === 'perdida') {
            $this->estado = $estado;
        }
    }

    public function setFCreacion($f_creacion) {
        $this->f_creacion = $f_creacion;
    }

    public function setUsuarioResponsable($usuario_responsable) {
        $this->usuario_responsable = $usuario_responsable;
    }
}

?>