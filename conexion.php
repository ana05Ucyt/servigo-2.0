<?php
$conexion= mysqli_connect("localhost", "root", "", "Servigo");
if(!$conexion){
    die("Error de conexion: " . mysqli_connect_error());
}
?>