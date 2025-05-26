<?php
include 'conexion.php';

// Obtener los datos del formulario
$idProducto = $_POST['id_producto'];
$cantidad = $_POST['cantidad'];

// Verificar si la cantidad es válida
if ($cantidad > 0) {
    // Obtener el stock actual del producto
    $consultaStock = "SELECT cantidad_producto FROM bodega WHERE id_producto = ?";
    $stmtStock = $conexion->prepare($consultaStock);
    $stmtStock->bind_param("i", $idProducto);
    $stmtStock->execute();
    $resultado = $stmtStock->get_result();
    $producto = $resultado->fetch_assoc();

    if ($producto) {
        // Sumar la cantidad al stock existente
        $nuevoStock = $producto['cantidad_producto'] + $cantidad;
        $consultaUpdate = "UPDATE bodega SET cantidad_producto = ? WHERE id_producto = ?";
        $stmtUpdate = $conexion->prepare($consultaUpdate);
        $stmtUpdate->bind_param("ii", $nuevoStock, $idProducto);
        $stmtUpdate->execute();

        // Redirigir de nuevo a la página de gestión de productos
        header("Location: gestionar_productos.php");
        exit();
    } else {
        echo "Producto no encontrado.";
    }
} else {
    echo "Cantidad inválida.";
}
