<?php

class DB {

    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $name = "crm";
    public $conexion;

    public function __construct() {
        $this->conexion = new mysqli(
            $this->host,
            $this->user,
            $this->pass,
            $this->name
        );
        if ($this->conexion->connect_errno) {
            die("<p>Error de conexión: " . $this->conexion->connect_error . "</p>");
        }
    }

    public function getConnection() {
        return $this->conexion;
    }
}
?>