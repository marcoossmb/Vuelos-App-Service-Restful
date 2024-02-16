<?php

require_once ('./db/DB.php');
require_once ('./models/PasajerosModel.php');
$pasajeros = new PasajerosModel();

@header("Content-type: application/json");

// Consultar GET
// devuelve o 1 o todos, dependiendo si recibe o no parámetro
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $res = $pasajeros->getAll();
    echo json_encode($res);
    exit();
}

// En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");
