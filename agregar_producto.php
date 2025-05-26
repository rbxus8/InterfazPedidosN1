<?php
include 'conexion.php';

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $idProducto = $_POST['id_producto'];
    $codigoProducto = $_POST['codigo_producto'];
    $nombre = $_POST['nombre'];
    $stock = $_POST['stock'];
    $local = $_POST['local'];

    // Validación de datos
    if (is_numeric($idProducto) && is_numeric($stock) && $stock > 0 && !empty($nombre)) {
        // Insertar el nuevo producto en la base de datos
        $query = "INSERT INTO productos (id_producto, codigo_producto, nombre) 
                  VALUES ('$idProducto', '$codigoProducto', '$nombre')";
        if ($conexion->query($query) === TRUE) {
            // Insertar el stock en la bodega del local seleccionado
            $queryStock = "INSERT INTO bodega (id_producto, cantidad_producto, id_local) 
                           VALUES ($idProducto, $stock, $local)";
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
