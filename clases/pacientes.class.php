<?php 

require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class pacientes extends conexion {

    private $table = 'pacientes';

    private $pacienteId = '';
    private $dni = '';
	private $nombre = '';
	private $direccion = '' ;
	private $codigoPostal = '';
	private $telefono = '';
	private $genero = '';
	private $fechaNacimiento = "0000-00-00";
    private $correo = '';
   	private $token = '';
   	private $imagen = '';
    // 574aa01c9b0f78dab12c68ecce6ec824 -> Token de pruba

    // Paginador
    public function paginadorPacientes($pagina = 1){

        $cantidad = 10;
        $inicio = 0;
        if($pagina > 1 ){
            $inicio = ($pagina -1 ) * $cantidad;
            
        }
        
        $query = "SELECT PacienteId, DNI , Nombre , Telefono , Correo FROM {$this->table} LIMIT $inicio, $cantidad";
        $datos = parent::obtenerDatos($query);
        return $datos;

    }

    // Obtener todo la info del paciente por un id
    public function obtenerPacienteId($id){
        $id = parent::escape($id);
        $query = "SELECT * FROM {$this->table} WHERE PacienteId = '{$id}' LIMIT 1";
        $datos = $this->obtenerDatos($query);
        return $datos;
    }

    // Guardar un nuevo paciente
    public function MethodPost($json){

        $_respuestas = new respuesta();
        // Transformar a array el json que nos llega 
        $datos = json_decode($json, true);
        
        if(!isset($datos['token'])){
            return $_respuestas->error_401('Necesitas un token');
        }else{
            // Nos llego el token
            $this->token = $datos['token'];
            $arrayToken = $this->buscarToken();
            // Validar que tenga datos el array
          if ($arrayToken) {
              // Asi se tienen que llamar los datos que me llegen del front
              if(!isset($datos['nombre'])  || !isset($datos['dni']) || !isset($datos['correo'])){
                  return $_respuestas->error_400();
              }else{
                 // Asignar valores a campos obligatorios y no obligatorios.
                  $this->nombre = parent::escape($datos['nombre']);
                  $this->dni = parent::escape($datos['dni']);
                  $this->correo = parent::escape($datos['correo']);
                  if(isset($datos['direccion'])){$this->direccion = parent::escape($datos['direccion']);}
                  if(isset($datos['codigopostal'])){ $this->codigoPostal = parent::escape($datos['codigopostal']);}
                  if(isset($datos['telefono'])){ $this->telefono = parent::escape($datos['telefono']);}
                  if(isset($datos['genero'])){ $this->genero = parent::escape($datos['genero']);}
                  if(isset($datos['fechanacimento'])){ $this->fechaNacimiento = parent::escape($datos['fechanacimento']);}
      
 
                 //Si exite una imagen la subimos al server y si no exite no hacemos nada
                 if(isset($datos['img'])){

                    $resp = $this->procesarImg($datos['img']);
                    $this->imagen = $resp;
                 } 



                  $resp =  $this->insertPaciente();
                  if($resp){
                      $repuesta = $_respuestas->response;
                      $repuesta['result'] = [ "Paciente" => $resp ];
                      return $repuesta;
                  }else{
      
                      return $_respuestas->error_500();
                  }
              }   

          } else {
              return $_respuestas->error_401('El token que se envio es invalido');
          }
          
        }

    }


    private function procesarImg($imagen){

        $direccion = dirname(__DIR__). "\public\img\\";
        $partes = explode(";base64," , $imagen );
        $extencion = explode('/', mime_content_type($imagen))[1];
        $imagen_base64 = base64_decode($partes[1]);
        $file = $direccion. uniqid(). "." . $extencion;
        file_put_contents($file,$imagen_base64);
        $nuevaDirecion = str_replace("\\", "/", $file);
        return $nuevaDirecion;

    }

    
    // Consulta sql para insertar un new Paciente
    private function insertPaciente(){
        $query = "INSERT INTO {$this->table} 
            (DNI,
            Nombre,
            Direccion,
            CodigoPostal,
            Telefono,
            Genero,
            FechaNacimiento,
            Correo,
            img)
         VALUES(
             '$this->dni',
             '$this->nombre',
             '$this->direccion',
             '$this->codigoPostal',
             '$this->telefono',
             '$this->genero',
             '$this->fechaNacimiento',
             '$this->correo',
             '$this->imagen')";
        $resp = parent::nonQueryId($query);
        if($resp){
            return $resp;
        }else{
            return 0;
        }
    }

    // Datos para procesar
    // Guardar un nuevo paciente
    public function MethodPut($json){

        $_respuestas = new respuesta();
        $datos = json_decode($json, true);
        if (!isset($datos['token'])) {
            return $_respuestas->error_401('Necesitas un token');
        }else{
            // Nos llego el token
            $this->token = $datos['token'];
            $arrayToken = $this->buscarToken();

            if ($arrayToken) {
                // Asi se tienen que llamar los datos que me llegen del front
            if (!isset($datos['pacienteId'])) {
                return $_respuestas->error_400();
            } else {
                // Validar si nos llega campos no oblitorias
                $this->pacienteId = $datos['pacienteId'];
                if (isset($datos['nombre'])) {
                    $this->nombre = parent::escape($datos['nombre']);
                }
                if (isset($datos['dni'])) {
                    $this->dni = parent::escape($datos['dni']);
                }
                if (isset($datos['correo'])) {
                    $this->correo = parent::escape($datos['correo']);
                }
                if (isset($datos['direccion'])) {
                    $this->direccion = parent::escape($datos['direccion']);
                }
                if (isset($datos['codigopostal'])) {
                    $this->codigoPostal = parent::escape($datos['codigopostal']);
                }
                if (isset($datos['telefono'])) {
                    $this->telefono = parent::escape($datos['telefono']);
                }
                if (isset($datos['genero'])) {
                    $this->genero = parent::escape($datos['genero']);
                }
                if (isset($datos['fechanacimento'])) {
                    $this->fechaNacimiento = parent::escape($datos['fechanacimento']);
                }

                $resp =  $this->Update();
                if ($resp) {
                    $repuesta = $_respuestas->response;
                    $repuesta['result'] = ["Paciente" => $this->pacienteId];
                    return $repuesta;
                } else {

                    return $_respuestas->error_500();
                }
            }
        }else{

            return $_respuestas->error_401('El token enviado es invalido o ya caduco');
        }

        }
       
    }

    // Quety para actualizar un paciente
    private function Update(){
        $query = "UPDATE {$this->table} SET
                  DNI   = '{$this->dni}',
                  Nombre = '{$this->nombre}',
                  Direccion = '{$this->direccion}',
                  CodigoPostal = '{$this->codigoPostal}',
                  Telefono = '{$this->telefono}',
                  Genero = '{$this->genero}',
                  FechaNacimiento = '{$this->fechaNacimiento}',
                  Correo = '{$this->correo}'
                  WHERE PacienteId  = {$this->pacienteId} LIMIT 1";
        $resp = parent::nonQuery($query);
        if ($resp >= 1) {
            return $resp;
        } else {
            return 0;
        }

    }

    /**
     * Se ocupa cuando la peticon es 
     * 
     */
    public function MethodDelete($json){
        $_respuestas = new respuesta();
        $datos = json_decode($json, true);

        if(!isset($datos['token'])){
            return $_respuestas->error_401('Token Invalido ó ya expiro');

        }else{
            $this->token = $datos['token'];
            $arrayToken = $this->buscarToken();
            if($arrayToken){
                  // Asi se tienen que llamar los datos que me llegen del front
                if (!isset($datos['pacienteId'])) {
                    return $_respuestas->error_400('Holas');
                } else {
                    // Validar si nos llega campos no oblitorias
                    $this->pacienteId = parent::escape($datos['pacienteId']);

                    // Metodo Eliminar 
                    $resp =  $this->delete();
                    if ($resp) {
                        $repuesta = $_respuestas->response;
                        $repuesta['result'] = ["Registro eliminado"];
                        return $repuesta;
                    } else {

                        return $_respuestas->error_500();
                    }
                }

            }else{
                return $_respuestas->error_401('El token enviado es invalido o ha caducado');
            }

        }

    }


    /**
     *  Es metodo tiene el query que 
     *  elimina un paciente.
     *  y lo llamamos el la function methodDelete
     */
    private function delete(){

        $query = "DELETE FROM {$this->table}  WHERE PacienteId = {$this->pacienteId} LIMIT 1";
        $resp = parent::nonQuery($query);
        if($resp >= 1){
            return $resp;
        }else{
            return 0;
        }

    }

    /**
     * Este metodo sirve para que haga una busqueda
     * y ver si el token que estamos mandando aun exite o
     * o esta inactivo.
     */
    private function buscarToken(){
        $query = "SELECT TokenId, UsuarioId,Estado FROM usuarios_token WHERE Token = '{$this->token}' AND Estado = 'Activo' LIMIT 1";
        $resp = parent::obtenerDatos($query);
        if($resp){
            return $resp;
        }else{
            return 0;
        }
    }

    private function actualizartoken($tokenId){
        $date = $this->date;
        $query = "UPDATE usuarios_token SET FECHA  = '{$date}' WHERE Token = '{$tokenId}' LIMIT 1";
        $result = parent::nonQuery($query);
        $resp = ($result >= 1) ? $result : 0 ; 
        return $resp;
    }


}
