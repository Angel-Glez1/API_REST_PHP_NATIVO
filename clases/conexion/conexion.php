<?php 
/*
|-------------------------------------------------------
|   Interaccion con la BASE DE DATOS
|-------------------------------------------------------
|   Esta clase tiene la conexion ala base de datos 
|   y los metodos para haceR
|   INSERT, DELETE , UPDATE , SELECT (CRUD)
|
*/

date_default_timezone_set('America/Mexico_city');  

class conexion{



    private $server;
    private $user;
    private $password;
    private $database;
    private $port;
    private $conexion;
    public  $date; 


    // Establecer conexion ala DDBB
    public function __construct() {

        // obtener los datos de configuracion para la DDBB 
        $listados = $this->DatosConexion();
        foreach ($listados as  $value) {
            $this->server    =  $value['server'];
            $this->user      =  $value['user'];
            $this->password  =  $value['password'];
            $this->database  =  $value['database'];
            $this->port      =  $value['port'];
        }
        $this->date = date('Y-m-d H:i');

        // Instanciar la conexion.
        $this->conexion = new mysqli($this->server, $this->user, $this->password, $this->database,$this->port);
        if($this->conexion->connect_error)
        {
            echo $this->conexion->connect_error;
            die();
        }

    }

    //inyecciones sql
    public function escape($dato)
    {
        return $this->conexion->real_escape_string($dato);
    } 


    // Imprimir los arrays con un print_r.
    public function Debug($array)
    {
        echo "<pre>";
        print_r($array);
        echo "</pre>";
    }

    // Obtener datos del archivo config para establecer la conexion ala DDBB.
    private function DatosConexion()
    {    
        // Leer el archivo confg para leer 
        $direccion = dirname(__FILE__);
        $jsonData = file_get_contents($direccion."/". "config" );
        
        return json_decode($jsonData,true);
    }

    // Convertir a UTF8 
    private function convertirUtf8($array)
    {
        array_walk_recursive($array, function(&$item, $key){
            if(!mb_detect_encoding($item, 'utf-8', true)){
                $item = utf8_encode($item);
            }
        });
        return $array;
    }

    //Obtener datos de una tabla(SELECT).
    public function obtenerDatos($query){

        $result = $this->conexion->query($query);
        $resultArray = array();
        foreach($result as $key){

            $resultArray[] = $key; 
        }

        return $this->convertirUtf8($resultArray);
    }


    // Guardar registros (INSERT).
    public function nonQuery($query)
    {
        $result = $this->conexion->query($query);
        return $this->conexion->affected_rows;
    }

    // Guardar registros y obtener el ultimo id(INSERT).
    public function nonQueryId($query)
    {

        $result = $this->conexion->query($query);
        $filas =  $this->conexion->affected_rows;
        if($filas >= 1 ){

            return $this->conexion->insert_id;

        }else{

            return 0;
        }
    }
    
    // Encriptar contraseña
    protected function encriptar($string){
        return md5($string);
    }



}

