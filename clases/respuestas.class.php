<?php  

class respuesta{

    // Respuestas a las solicitados
    public $response = [
        "status" => 'ok',
        "result" => []
    ];

    // Error de REQUEST a un metodo no permitido
    public function error_405(){

        $this->response['status'] = 'error';
        $this->response['result'] = [
            "error_id" => "405",
            "error_msg" => "Metodo No permitido"
        ];
        
        return $this->response;
    }
    // Error de REQUEST a datos incompletos
    public function error_400(){

        $this->response['status'] = 'error';
        $this->response['result'] = [
            "error_id" => "400",
            "error_msg" => "Datos Incompletos o formato incorrecto"
        ];
        
        return $this->response;
    }

    // Error de REQUEST a un metodo exitente pero un error al ejecutar
    public  function error_200($string = "Datos Incorrectos"){

        $this->response['status'] = 'error';
        $this->response['result'] = [
            "error_id" => "200",
            "error_msg" => $string
        ];
        
        return $this->response;
    }
    public  function error_500($string = "Error Interno del servidor"){

        $this->response['status'] = 'error';
        $this->response['result'] = [
            "error_id" => "500",
            "error_msg" => $string
        ];
        
        return $this->response;
    }
    public  function error_401($string = "No autorizado"){

        $this->response['status'] = 'error';
        $this->response['result'] = [
            "error_id" => "401",
            "error_msg" => $string
        ];
        
        return $this->response;
    }




}



?>