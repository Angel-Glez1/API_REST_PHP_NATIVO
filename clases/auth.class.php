<?php 
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

/*
|--------------------------------------------------------
|   Autetificacion de usuarios.
|--------------------------------------------------------
|   Esta clase resive los datos del front-end (POST-MAN) 
|   al momento que un usuario Inicia Session.
|   
|
*/

class auth extends conexion{

    // Procesar datos que nos llega al momento que un usuario se loguea.
    public function login($json)
    {
        $_respuestas = new respuesta();
        // Guardamos los datos para procesar los 
        $datos = json_decode($json,true); 
        if(!isset($datos['user'])  || !isset($datos['password']) ){
            // Error con los datos
            return $_respuestas->error_400();
        }else{
            // Obtenemos el usuario y el paswword
            $correo = $datos['user'];
            $password = $datos['password'];
            $password = parent::encriptar($password);
            $datosUser = $this->obtenerDatosUsuario($correo);
            if($datosUser){
                // Verificar contraseña
                if($password == $datosUser[0]['Password']){
                    // Crear token
                    if($datosUser[0]['Estado'] == 'Activo' ){
                        $verficar = $this->makeToken($datosUser[0]['UsuarioId']);
                        // Verificar que se guardo el token
                        if($verficar){
                            $result = $_respuestas->response;
                            $result['result'] = ['token' => $verficar];
                            return $result;
                        }else{
                            // Nose guardo el token.
                            $_respuestas->error_500("No se puedo guardar");
                        }


                    }else{

                        return $_respuestas->error_200("Tu cuenta ha sido suspendida");
                    }
                    
                }else{
                    // Password Invalido
                    return $_respuestas->error_200("Password Incorreto");
                }

            }else{
                // Si no exite.
                return $_respuestas->error_200("El $correo no exite");
            }

        }

    }


    // Exite el usuario que solicita el logueo
    private function obtenerDatosUsuario($email)
    {
        $query = "SELECT UsuarioId, Password ,Estado FROM usuarios WHERE Usuario = '$email' LIMIT 1";
        $datos = parent::obtenerDatos($query);

        // Si exite el usario retornamos sus datos y si no un 0
        if(isset($datos[0]['UsuarioId'])){
            return $datos; 
        } else{
            return  0;
        }
    }


    // Crear Token para que el usuario pueda hacer REQUEST SEGURAS
    private function makeToken($user_id){
        // El bin2Hex convierte un string en hexageximal
        $val = true;
        $token = bin2hex(openssl_random_pseudo_bytes(16, $val));
        $estadoToken = 'Activo';
        $date = $this->date;
        $query = "INSERT INTO usuarios_token (UsuarioId,Token,Estado,Fecha) VALUES ('$user_id','$token','$estadoToken','$date')";
        $verficar = parent::nonQuery($query);
        if($verficar){
            return $token;
        }else{
            return 0 ;
        }

    }


}




?>