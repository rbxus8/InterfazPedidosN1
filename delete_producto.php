<?php
include 'db_connection.php'; // Incluye la conexión

$id = $_GET['id'];

$sql = "DELETE FROM productos WHERE id_producto = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "Producto eliminado exitosamente.";
} else {
    echo "Error al eliminar producto: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
