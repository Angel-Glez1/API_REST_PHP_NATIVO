<?php 
/*
|---------------------------------------------------
|       Crud De pacientes
|----------------------------------------------------
|
|   Estos archivo son el controlador x por que     
|   solo resiven la solicutud http verificar que 
|   sea el metodo que corresponde y hace la 
|   solicitud al back-end y asu vez le entrega una respuesta
|   Al Front-end
|
*/
require_once 'clases/pacientes.class.php';
require_once 'clases/respuestas.class.php';
// Instaciar las clases
$_paciente = new pacientes();
$_repuesta = new respuesta();

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    // Mostrar pacientes

    if(isset($_GET['page'])){
        // Todos los pacientes
        $page = $_GET['page'];
        $jsonPaciente = $_paciente->paginadorPacientes($page);
        header("Content-Type: application/json");
        echo json_encode($jsonPaciente);
        http_response_code(200);

    }elseif (isset($_GET['id'])){
        // Por si id
        $id =  $_GET['id'];
        $jsonPaciente = $_paciente->obtenerPacienteId($id);
        header("Content-Type: application/json");
        echo json_encode($jsonPaciente);
        http_response_code(200);

    }

}else if ($_SERVER['REQUEST_METHOD'] == 'POST'){

    // Obtener datos
    $postBody = file_get_contents("php://input");

    // Mandar al back-end
    $datosArray = $_paciente->MethodPost($postBody);

    // Mandaer repuestas al front-end
    header("Content-Type: application/json");
    if(isset($datosArray['result']['error_id'])){
        $r = $datosArray['result']['error_id'];
        http_response_code($r);
    }else{
        http_response_code(200);
    }

    echo json_encode($datosArray);
    

}else if ($_SERVER['REQUEST_METHOD'] == 'PUT'){
    // Recibir datos 
    $postBody = file_get_contents("php://input");

    // Mandamo al back-end
    $datosArray = $_paciente->MethodPut($postBody);

    // Mandaer repuestas al front-end
    header("Content-Type: application/json");
    if (isset($datosArray['result']['error_id'])) {
        $r = $datosArray['result']['error_id'];
        http_response_code($r);
    } else {
        http_response_code(200);
    }

    echo json_encode($datosArray);
    

}else if($_SERVER['REQUEST_METHOD'] == 'DELETE'){

    $headers = getallheaders();
    if(isset($headers['token']) && isset($headers['pacienteId'])){
        // Resivir datos por los header.
        $send = ['token' => $headers['token'], 'pacienteId' => $headers['pacienteId'] ];
        $postBody = json_encode($send);

    }else{
        // Resivimos datos por el body
        $postBody = file_get_contents("php://input");
    }

    // Mandar al back-end
    $datosArray = $_paciente->MethodDelete($postBody);

    // Mandaer repuestas al front-end
    header("Content-Type: application/json");
    if (isset($datosArray['result']['error_id'])) {
        $r = $datosArray['result']['error_id'];
        http_response_code($r);
    } else {
        http_response_code(200);
    }
    echo json_encode($datosArray);
    
}else{
    // Metodo no exitente
    
    header("Content-Type: application/json");
    $datosArray = $_response->error_405();
    echo json_encode($datosArray);

}

?>