<?php

// Incluir base de datos y el modelo del pasajero
require_once ('./db/DB.php');
require_once ('./models/PasajerosModel.php');

$pasajeros = new PasajerosModel();

// Establecer la cabecera de la respuesta como JSON
@header("Content-type: application/json");

// Consultar GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $res = $pasajeros->getAll();
    echo json_encode($res);
    exit();
}

// En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");
