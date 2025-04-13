<?php
$conexion = new mysqli("localhost", "root", "", "uwu");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener los datos enviados por AJAX
$datos = json_decode(file_get_contents("php://input"), true);

// Recorremos los datos y actualizamos la base de datos
foreach ($datos as $producto) {
    $id = $producto['id'];
    $codigo = $producto['codigo'];
    $nombre = $producto['nombre'];
    $cantidad = $producto['cantidad'];
    $precio = $producto['precio'];

    // Actualizar producto en la base de datos
    $conexion->query("
        UPDATE productos 
        SET codigo_producto = '$codigo', nombre = '$nombre', precio = '$precio' 
        WHERE id_producto = $id
    ");

    // Actualizar cantidad en bodega
    $conexion->query("
        UPDATE bodega 
        SET cantidad_producto = $cantidad 
        WHERE id_producto = $id
    ");
}

echo json_encode(['success' => true]);
?>
