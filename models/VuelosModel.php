<?php

// Definición de la clase VuelosModel, que extiende de DB
class VuelosModel extends DB {

    // Declaración de propiedades privadas
    private $table;
    private $conexion;

    // Constructor de la clase
    public function __construct() {
        $this->table = "vuelo";
        $this->conexion = $this->getConexion();
    }

    /**
     * Método para obtener los detalles de un vuelo específico por su identificador
     *
     * @param string $nuvuel Identificador del vuelo
     * @return array|string Retorna un array con los detalles del vuelo si se encuentra,
     * "SIN DATOS" si no se encuentra información o un mensaje de error en caso contrario.
     */
    public function getUnVuelo($nuvuel) {
        try {
            $sql = "SELECT v.identificador, ao.codaeropuerto AS 'aeroorigen', ao.nombre AS 'aeronameorigen', ao.pais AS 'paisorigen',
                    ae.codaeropuerto AS 'aerodestino', ae.nombre AS 'aeronamedestino', ae.pais AS 'paisdestino', v.tipovuelo, COUNT(p.idpasaje) AS 'idpasaje' 
                    FROM vuelo v JOIN aeropuerto ao ON v.aeropuertoorigen = ao.codaeropuerto JOIN aeropuerto ae 
                    ON v.aeropuertodestino = ae.codaeropuerto LEFT JOIN pasaje p ON v.identificador = p.identificador WHERE v.identificador=?
                    GROUP BY v.identificador;";
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

    /**
     * Método para obtener todos los vuelos con sus detalles
     *
     * @return array|string Retorna un array con los detalles de todos los vuelos si se encuentran,
     * o un mensaje de error en caso contrario.
     */
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
