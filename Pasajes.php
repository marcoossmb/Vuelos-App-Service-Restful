<?php

// Incluir base de datos y el modelo del pasaje
require_once ('./db/DB.php');
require_once ('./models/PasajesModel.php');
$pasaje = new PasajesModel();

// Establecer la cabecera de la respuesta como JSON
@header("Content-type: application/json");

// Consultar GET
// devuelve o 1 o todos, dependiendo si recibe o no parámetro
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $res = $pasaje->getUnPasaje($_GET['id']);
        echo json_encode($res);
        exit();
    } elseif (isset($_GET['identificador'])) {
        $res = $pasaje->getPasajesPorIdentificador($_GET['identificador']);
        echo json_encode($res);
        exit();
    } else {
        $res = $pasaje->getAll();
        echo json_encode($res);
        exit();
    }
}

// Borrar DELETE
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $id = $_GET['id'];
    $res = $pasaje->borrar($id);
    $resul['resultado'] = $res;
    echo json_encode($resul);
    exit();
}

// Crear un nuevo reg POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // se cargan toda la entrada que venga en php://input
    $post = json_decode(file_get_contents('php://input'), true);
    $res = $pasaje->guardar($post);
    $resul['resultado'] = $res;
    echo json_encode($resul);
    exit();
}

// Actualizar PUT, se reciben los datoc como en el put
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if (isset($_GET['id'])) {
        $put = json_decode(file_get_contents('php://input'), true);
        $res = $pasaje->actualiza($put, $_GET['id']);
        $resul['mensaje'] = $res;
        echo json_encode($resul);
        exit();
    }
}

// En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");
