<?php

// Definición de la clase PasajesModel, que extiende de DB
class PasajesModel extends DB {

    // Declaración de propiedades privadas
    private $table;
    private $conexion;

    // Constructor de la clase
    public function __construct() {
        $this->table = "pasaje";
        $this->conexion = $this->getConexion();
    }

    /**
     * Método para obtener todos los pasajes
     *
     * @return array|string Retorna un array con los registros de pasajes si la consulta es exitosa,
     * o un mensaje de error si ocurre algún problema.
     */
    public function getAll() {
        try {
            // Consultas SQL para obtener registros de diferentes tablas relacionadas            
            $sql1 = "SELECT p.*, per.nombre FROM $this->table p JOIN pasajero per ON p.pasajerocod = per.pasajerocod ORDER BY p.idpasaje;";
            $statement1 = $this->conexion->query($sql1);
            $registros1 = $statement1->fetchAll(PDO::FETCH_ASSOC);

            $sql2 = "SELECT nombre, pasajerocod FROM pasajero GROUP BY pasajerocod;";
            $statement2 = $this->conexion->query($sql2);
            $registros2 = $statement2->fetchAll(PDO::FETCH_ASSOC);

            $sql3 = "SELECT identificador, aeropuertoorigen, aeropuertodestino FROM vuelo";
            $statement3 = $this->conexion->query($sql3);
            $registros3 = $statement3->fetchAll(PDO::FETCH_ASSOC);

            // Retorna el array de registros en formato JSON
            return array("registros1" => $registros1, "registros2" => $registros2, "registros3" => $registros3);
        } catch (PDOException $e) {
            return "ERROR AL CARGAR.<br>" . $e->getMessage();
        }
    }

    /**
     * Método para obtener un pasaje específico por su número de pasaje
     *
     * @param string $nupasaje Número de pasaje a buscar
     * @return array|string Retorna un array con los datos del pasaje si se encuentra,
     * o un mensaje de error si ocurre algún problema.
     */
    public function getUnPasaje($nupasaje) {
        try {
            // Consulta SQL para obtener un pasaje específico            
            $sql = "SELECT * FROM $this->table WHERE idpasaje=?";
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->bindParam(1, $nupasaje);
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
     * Función privada para realizar comprobaciones antes de insertar o actualizar un pasaje
     *
     * @param string $pasajerocod Código del pasajero
     * @param string $identificador Identificador del vuelo
     * @param int $numasiento Número de asiento
     * @return array Resultados de las comprobaciones
     */
    private function comprobaciones($pasajerocod, $identificador, $numasiento) {
        $resultados = [];

        // Comprobación de existencia de pasajero en el vuelo
        $sql1 = "SELECT * FROM $this->table WHERE pasajerocod = ? AND identificador = ?";
        $stmt1 = $this->conexion->prepare($sql1);
        $stmt1->execute([$pasajerocod, $identificador]);
        $resultados['pasajero_vuelo_existente'] = $stmt1->fetch();

        // Comprobación de asiento ocupado
        $sql2 = "SELECT * FROM $this->table WHERE numasiento = ? AND identificador = ?";
        $stmt2 = $this->conexion->prepare($sql2);
        $stmt2->execute([$numasiento, $identificador]);
        $resultados['asiento_ocupado'] = $stmt2->fetch();

        return $resultados;
    }

    /**
     * Método para borrar un pasaje por su número de pasaje
     *
     * @param int $pasajeno Número de pasaje a borrar
     * @return bool|string Retorna true si el borrado es exitoso, false si no se encuentra el pasaje o un mensaje de error en caso contrario.
     */
    public function borrar($pasajeno) {
        try {
            $sql = "DELETE FROM $this->table WHERE idpasaje = ?";
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->bindParam(1, $pasajeno);
            $sentencia->execute();
            if ($sentencia->rowCount() == 0)
                return false;
            else
                return true;
        } catch (PDOException $e) {
            return "ERROR AL BORRAR.<br>" . $e->getMessage();
        }
    }

    /**
     * Método para insertar un nuevo pasaje
     *
     * @param array $post Datos del pasaje a guardar
     * @return string Mensaje de éxito o error al guardar el pasaje
     */
    public function guardar($post) {
        try {
            $comprobaciones = $this->comprobaciones($post['pasajerocod'], $post['identificador'], $post['numasiento']);

            if ($comprobaciones['pasajero_vuelo_existente']) {
                return "ERROR AL INSERTAR. EL PASAJERO " . $post['pasajerocod'] . " YA ESTÁ EN EL VUELO " . $post['identificador'];
            }

            if ($comprobaciones['asiento_ocupado']) {
                return "ERROR AL INSERTAR. EL NÚMERO DE ASIENTO " . $post['numasiento'] . " YA ESTÁ OCUPADO EN EL VUELO " . $post['identificador'];
            }

            // Inserción del pasaje
            $sql_insert = "INSERT INTO $this->table (pasajerocod, identificador, numasiento, clase, pvp) VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $this->conexion->prepare($sql_insert);
            $stmt_insert->execute([$post['pasajerocod'], $post['identificador'], $post['numasiento'], $post['clase'], $post['pvp']]);
            return "REGISTRO INSERTADO CORRECTAMENTE";
        } catch (PDOException $e) {
            return "ERROR SQL al insertar: " . $e->getMessage();
        }
    }

    /**
     * Método para actualizar un pasaje existente
     *
     * @param array $put Datos actualizados del pasaje
     * @param int $idpasaje ID del pasaje a actualizar
     * @return string Mensaje de éxito o error al actualizar el pasaje
     */
    public function actualiza($put, $idpasaje) {
        try {
            $comprobaciones = $this->comprobaciones($put['pasajerocod'], $put['identificador'], $put['numasiento']);

            if ($comprobaciones['pasajero_vuelo_existente']) {
                return "ERROR AL ACTUALIZAR. EL PASAJERO " . $put['pasajerocod'] . " YA ESTÁ EN EL VUELO " . $put['identificador'];
            }

            if ($comprobaciones['asiento_ocupado']) {
                return "ERROR AL ACTUALIZAR. EL NÚMERO DE ASIENTO " . $put['numasiento'] . " YA ESTÁ OCUPADO EN EL VUELO " . $put['identificador'];
            }

            // Actualización del pasaje
            $sql_update = "UPDATE $this->table SET pasajerocod = ?, identificador = ?, numasiento = ?, clase = ?, pvp = ? WHERE idpasaje = ?";
            $stmt_update = $this->conexion->prepare($sql_update);
            $stmt_update->execute([$put['pasajerocod'], $put['identificador'], $put['numasiento'], $put['clase'], $put['pvp'], $idpasaje]);

            if ($stmt_update->rowCount() > 0) {
                return "REGISTRO ACTUALIZADO CORRECTAMENTE";
            } else {
                return "ERROR AL ACTUALIZAR. NO SE ENCONTRO EL PASAJE AL ACTUALIZAR";
            }
        } catch (PDOException $e) {
            return "ERROR SQL al actualizar: " . $e->getMessage();
        }
    }

    /**
     * Método para obtener pasajes por su identificador de vuelo
     *
     * @param string $nupasaje Identificador de vuelo
     * @return array|bool|string Retorna un array con los registros de pasajes si se encuentran,
     * false si no se encuentran registros o un mensaje de error en caso contrario.
     */
    public function getPasajesPorIdentificador($nupasaje) {
        try {
            $sql1 = "SELECT * FROM pasaje WHERE identificador = ?;";
            $sentencia1 = $this->conexion->prepare($sql1);
            $sentencia1->bindParam(1, $nupasaje);
            $sentencia1->execute();
            $registros1 = $sentencia1->fetchAll(PDO::FETCH_ASSOC);

            $sql2 = "SELECT ps.* FROM pasaje p JOIN pasajero ps ON p.pasajerocod = ps.pasajerocod WHERE p.identificador = ?;";
            $sentencia2 = $this->conexion->prepare($sql2);
            $sentencia2->bindParam(1, $nupasaje);
            $sentencia2->execute();
            $registros2 = $sentencia2->fetchAll(PDO::FETCH_ASSOC);

            if ($registros1 && $registros2) {
                return array("registros1" => $registros1, "registros2" => $registros2);
            }
            return false;
        } catch (PDOException $e) {
            return "ERROR AL CARGAR.<br>" . $e->getMessage();
        }
    }
}
