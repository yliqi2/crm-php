<?php

require_once __DIR__ . '/../config/config.php';

class DB {


    public $conexion;

    public function __construct() {
        $this->conexion = new mysqli(
            Config::DB_HOST,
            Config::DB_USER,
            Config::DB_PASS,
            Config::DB_NAME
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