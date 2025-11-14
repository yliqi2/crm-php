<?php

class Cliente {

    private $id_cliente;
    private $nombre_completo; //varchar 100
    private $email; //varchar 75
    private $tlf; //int 15
    private $empresa; //varchar 100
    private $fecha_creacion; // datetime
    private $usuario_responsable; //id_usuario

    public function __construct($id_cliente , $nombre_completo , $email , $tlf , $empresa , $fecha_creacion , $usuario_responsable) {
        $this->id_cliente = $id_cliente;
        $this->nombre_completo = $nombre_completo;
        $this->email = $email;
        $this->tlf = $tlf;
        $this->empresa = $empresa;
        $this->fecha_creacion = $fecha_creacion;
        $this->usuario_responsable = $usuario_responsable;
    }

    // Getters
    public function getIdCliente() {
        return $this->id_cliente;
    }

    public function getNombreCompleto() {
        return $this->nombre_completo;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getTlf() {
        return $this->tlf;
    }

    public function getEmpresa() {
        return $this->empresa;
    }

    public function getFechaCreacion() {
        return $this->fecha_creacion;
    }

    public function getUsuarioResponsable() {
        return $this->usuario_responsable;
    }

    // Setters
    public function setIdCliente($id_cliente) {
        $this->id_cliente = $id_cliente;
    }

    public function setNombreCompleto($nombre_completo) {
        $this->nombre_completo = $nombre_completo;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setTlf($tlf) {
        $this->tlf = $tlf;
    }

    public function setEmpresa($empresa) {
        $this->empresa = $empresa;
    }

    public function setFechaCreacion($fecha_creacion) {
        $this->fecha_creacion = $fecha_creacion;
    }

    public function setUsuarioResponsable($usuario_responsable) {
        $this->usuario_responsable = $usuario_responsable;
    }
}

?>
