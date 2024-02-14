<?php
require_once ('./db/DB.php');
require_once ('./models/VuelosModel.php');
$vuel = new VuelosModel();

@header("Content-type: application/json");

// Consultar GET
// devuelve o 1 o todos, dependiendo si recibe o no parámetro
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $res = $vuel->getUnVuelo($_GET['id']);
        echo json_encode($res);
        exit();
    } else {
        $res = $vuel->getAll();
        echo json_encode($res);
        exit();
    }
}

// En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");