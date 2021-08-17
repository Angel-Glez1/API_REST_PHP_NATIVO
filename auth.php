<?php  
header("Acces-Controll-Allow-Origin: *");
require_once 'clases/auth.class.php';
require_once 'clases/respuestas.class.php';


$_auth = new auth();
$_response = new respuesta();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Resivimos datos
    $postBody = file_get_contents("php://input");

    // Mandamos los datos al Back-end(Para Procesarlos).
    $datosArray = $_auth->login($postBody);

    // Devolver una respuesta al Fron-end
    header("Content-Type: application/json");
    if(isset($datosArray['result']['error_id'])){ // Validar si hubo un error
        $reponseCode = $datosArray['result']['error_id'];
        http_response_code($reponseCode);

    }else{
        http_response_code(200);
    }
    
    echo json_encode($datosArray);

}else{
    // Si el metodo no es post
    header("Content-Type: application/json");
    $datosArray = $_response->error_405();
    echo json_encode($datosArray);

}



?>

