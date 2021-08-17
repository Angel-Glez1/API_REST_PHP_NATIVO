<?php
date_default_timezone_set('America/Mexico_city');  

require_once '../clases/token.class.php';

$_toke = new token();
$fecha = date("Y-m-d H:i");
echo $_toke->actualizarToken($fecha);




?>