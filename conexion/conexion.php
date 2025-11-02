<?php
// Verifica si la conexión ya existe antes de crear una nueva
if (!isset($conexion) || $conexion === null) {
    $host = "b1xbvdktlo20sdr39wbi-mysql.services.clever-cloud.com";
    $usuario = "udqgmhwed2gjzprz";
    $contrasena = "5pDRSAyLkyoXQW28HNBK";
    $baseDatos = "b1xbvdktlo20sdr39wbi";

    // Crear la conexión
    $conexion = new mysqli($host, $usuario, $contrasena, $baseDatos);

    // Verificar si hubo error en la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

  
}
//prueba cambio en git usuario