<?php
include 'conexion/conexion.php';

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
    // Si no se envía codigo_producto, se genera uno único automáticamente
    $codigo_producto = isset($_POST['codigo_producto']) && !empty($_POST['codigo_producto']) ? $_POST['codigo_producto'] : uniqid('PROD-');
    $stock = isset($_POST['stock']) ? $_POST['stock'] : null;
    $local = isset($_POST['local']) ? $_POST['local'] : null;
    $precio = isset($_POST['precio']) ? $_POST['precio'] : null; // Nuevo campo precio

    // Validación de datos
    if (
        !empty($nombre) &&
        !empty($codigo_producto) &&
        !empty($stock) && is_numeric($stock) && $stock > 0 &&
        !empty($local) &&
        !empty($precio) && is_numeric($precio) && $precio > 0
    ) {
        // Insertar el nuevo producto en la base de datos (id_producto autoincremental)
        $query = "INSERT INTO productos (codigo_producto, nombre, precio, estado) 
          VALUES ('$codigo_producto', '$nombre', $precio, 1)";






        if ($conexion->query($query) === TRUE) {
            // Obtener el id_producto generado automáticamente
            $id_producto = $conexion->insert_id;

            // Insertar el stock en la bodega del local seleccionado
            $queryStock = "INSERT INTO bodega (id_producto, cantidad_producto, id_local) 
                           VALUES ($id_producto, $stock, $local)";
            if ($conexion->query($queryStock) === TRUE) {
                echo "Producto agregado correctamente";
            } else {
                echo "Error al agregar stock: " . $conexion->error;
            }
        } else {
            echo "Error al agregar el producto: " . $conexion->error;
        }
    } else {
        echo "Por favor, ingrese datos válidos.";
    }
}

$conexion->close();
