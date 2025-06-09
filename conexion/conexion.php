<?php
$host = "b1xbvdktlo20sdr39wbi-mysql.services.clever-cloud.com";
$usuario = "udqgmhwed2gjzprz";
$contrasena = "5pDRSAyLkyoXQW28HNBK";
$baseDatos = "b1xbvdktlo20sdr39wbi";

// Crear la conexión
$conexion = new mysqli($host, $usuario, $contrasena, $baseDatos);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
