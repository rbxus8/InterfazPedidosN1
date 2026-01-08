<?php
// Verifica si la conexión ya existe antes de crear una nueva
if (!isset($conexion) || $conexion === null) {
    $host = "localhost";
    $usuario = "root";
    $contrasena = "";
    $baseDatos = "n";

// Crear la conexión
$conexion = new mysqli($host, $usuario, $contrasena, $baseDatos);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

  
}
//prueba cambio en git usuario