<?php
include 'conexion/conexion.php';

if (isset($_GET['id_local'])) {
    $id_local = intval($_GET['id_local']);

    $sql = "SELECT p.id_producto, p.nombre, p.precio, b.cantidad_producto
            FROM bodega b
            JOIN productos p ON b.id_producto = p.id_producto
            WHERE b.id_local = ? AND b.cantidad_producto > 0";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_local);
    $stmt->execute();
    $result = $stmt->get_result();

    $productos = [];
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($productos);
    exit;
} else {
    echo json_encode([]);
    exit;
}
