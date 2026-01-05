<?php
$host = "b1xbvdktlo20sdr39wbi-mysql.services.clever-cloud.com";
$usuario = "udqgmhwed2gjzprz";
$contrasena = "5pDRSAyLkyoXQW28HNBK";
$baseDatos = "b1xbvdktlo20sdr39wbi";

// evita abrir conexiones duplicadas
if (!isset($conexion)) {

    // intenta conectar
    $conexion = @new mysqli($host, $usuario, $contrasena, $baseDatos);

    // manejo de error de demasiadas conexiones
    if ($conexion->connect_errno) {

        if ($conexion->connect_errno == 1203) { // max_user_connections
            die("⚠️ El servidor tiene demasiadas conexiones abiertas. 
            Cierre pestañas o espere 1 minuto e intente nuevamente.");
        }

        die("❌ Error de conexión: " . $conexion->connect_error);
    }

    // fuerza cierre automático al finalizar script
    register_shutdown_function(function () use ($conexion) {
        if ($conexion) {
            $conexion->close();
        }
    });
}
