<?php

class VuelosModel extends DB {

    private $table;
    private $conexion;

    public function __construct() {
        $this->table = "vuelo";
        $this->conexion = $this->getConexion();
    }

    // Devuelve un array departamento
    public function getUnVuelo($nuvuel) {
        try {
            $sql = "SELECT * FROM $this->table WHERE identificador=?";
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->bindParam(1, $nuvuel);
            $sentencia->execute();
            $row = $sentencia->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return $row;
            }
            return "SIN DATOS";
        } catch (PDOException $e) {
            return "ERROR AL CARGAR.<br>" . $e->getMessage();
        }
    }

    public function getAll() {
        try {
            $sql = "SELECT v.identificador, ao.codaeropuerto AS 'aeroorigen', ao.nombre AS 'aeronameorigen', ao.pais AS 'paisorigen',
                    ae.codaeropuerto AS 'aerodestino', ae.nombre AS 'aeronamedestino', ae.pais AS 'paisdestino', v.tipovuelo, COUNT(p.idpasaje) AS 'idpasaje' 
                    FROM vuelo v JOIN aeropuerto ao ON v.aeropuertoorigen = ao.codaeropuerto JOIN aeropuerto ae 
                    ON v.aeropuertodestino = ae.codaeropuerto LEFT JOIN pasaje p ON v.identificador = p.identificador 
                    GROUP BY v.identificador;";
            
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
