<?php

// Se incluye el archivo de configuración.
require_once './config/Config.php';

// Clase abstracta para manejar la base de datos.
abstract class DB {

    // Propiedades para la conexión a la base de datos.
    private $servername = servername;
    private $database = database;
    private $username = username;
    private $password = password;
    private $conexion;
    private $mensajeerror = "";

    // Método para obtener la conexión a la base de datos.
    public function getConexion() {
        try {
            // Establece la conexión utilizando PDO.
            $this->conexion = new PDO("mysql:host=$this->servername;dbname=$this->database;charset=utf8",
                    $this->username, $this->password);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conexion;
        } catch (PDOException $e) {
            $this->mensajeerror = $e->getMessage();
        }
    }

    // Método para cerrar la conexión a la base de datos.
    public function closeConexion() {
        $this->conexion = null;
    }

    // Método para obtener el mensaje de error en caso de fallo en la conexión.
    public function getMensajeError() {
        return $this->mensajeerror;
    }
}
