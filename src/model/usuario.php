<?php

class Usuario {
    private $id_usuario;
    private $nombre_completo;
    private $email;
    private $contra;
    private $role;

    public function __construct($id_usuario = null, $nombre_completo = null, $email = null, $contra = null, $role = 'vendedor') {
        $this->id_usuario = $id_usuario;
        $this->nombre_completo = $nombre_completo;
        $this->email = $email;
        $this->contra = $contra;
        $this->role = $role;
    }


    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function getNombreCompleto() {
        return $this->nombre_completo;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getContra() {
        return $this->contra;
    }

    public function getRole() {
        return $this->role;
    }

    public function isAdmin() {
        return $this->role === 'admin';
    }

    public function setNombreCompleto($nombre_completo) {
        $this->nombre_completo = $nombre_completo;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setContra($contra) {
        $this->contra = $contra;
    }

    public function setRole($role) {
        $this->role = $role;
    }

    // Método para hashear la contraseña
    public function hashContra($contra) {
        $this->contra = password_hash($contra, PASSWORD_DEFAULT);
    }

    // Método para verificar la contraseña
    public function verificarContra($contra) {
        return password_verify($contra, $this->contra);
    }
}

?>