<?php
include 'conexion/conexion.php';

if (isset($_POST['id_producto'])) {
    $idProducto = intval($_POST['id_producto']);

    try {
        // Eliminar primero de bodega
        $stmtBodega = $conexion->prepare("DELETE FROM bodega WHERE id_producto = ?");
        $stmtBodega->bind_param("i", $idProducto);
        $stmtBodega->execute();

        // Luego eliminar de productos
        $stmtProducto = $conexion->prepare("DELETE FROM productos WHERE id_producto = ?");
        $stmtProducto->bind_param("i", $idProducto);
        $stmtProducto->execute();

        // Redirigir a la misma página sin mensaje
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } catch (Exception $e) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
} else {
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
