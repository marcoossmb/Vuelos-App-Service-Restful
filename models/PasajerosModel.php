<?php

// Definición de la clase PasajerosModel, que extiende de DB
class PasajerosModel extends DB {

    // Declaración de propiedades privadas    
    private $table;
    private $conexion;

    // Constructor de la clase
    public function __construct() {
        $this->table = "pasajero";
        $this->conexion = $this->getConexion();
    }

    /**
     * Método para obtener todos los registros de pasajeros
     *
     * @return array|string Retorna un array con los registros de pasajeros si la consulta es exitosa,
     * o un mensaje de error si ocurre algún problema.
     */
    public function getAll() {
        try {
            $sql = "SELECT * FROM $this->table";

            $statement = $this->conexion->query($sql);
            $registros = $statement->fetchAll(PDO::FETCH_ASSOC);
            $statement = null;
            // Retorna el array de registros
            return $registros;
        } catch (PDOException $e) {
            return "ERROR AL CARGAR.<br>" . $e->getMessage();
        }
    }
}