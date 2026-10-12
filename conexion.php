<?php
//VARIABLES DE CONEXION
$servername="localhost";
$nombreUsuario = "root";
$claveUsuario = "";
$nombreBaseDatos="safe_park";

//crear conexion y probarla
$conexion = new mysqli($servername, $nombreUsuario,
$claveUsuario, $nombreBaseDatos);

//prueba de conexion
if($conexion ->connect_error){
    echo"Error en la conexxion";


}//else{echo"Conexion exitosa";}
$conexion->set_charset("utf8");

?>