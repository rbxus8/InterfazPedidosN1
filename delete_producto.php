<?php
include 'conexion/conexion.php';

$id = $_GET['id'];

$sql = "DELETE FROM productos WHERE id_producto = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "Producto eliminado exitosamente.";
} else {
    echo "Error al eliminar producto: " . $stmt->error;
}

$stmt->close();
$conn->close();
