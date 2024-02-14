<?php

require_once './config/Config.php';

abstract class DB {

    private $servername = servername;
    private $database = database;
    private $username = username;
    private $password = password;
    private $conexion;
    private $mensajeerror = "";

    public function getConexion() {
        try {
            $this->conexion = new PDO("mysql:host=$this->servername;dbname=$this->database;charset=utf8",
                    $this->username, $this->password);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conexion;
        } catch (PDOException $e) {
            $this->mensajeerror = $e->getMessage();
        }
    }

    public function closeConexion() {
        $this->conexion = null;
    }

    public function getMensajeError() {
        return $this->mensajeerror;
    }
}
