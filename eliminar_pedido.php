<?php
include 'conexion.php';

// Obtener el ID del pedido a eliminar
$idPedido = isset($_GET['id']) ? $_GET['id'] : null;

if ($idPedido) {
    // Eliminar el pedido
    $consultaEliminar = "DELETE FROM pedidos WHERE id_pedido = ?";
    $stmt = $conexion->prepare($consultaEliminar);
    $stmt->bind_param("i", $idPedido);

    if ($stmt->execute()) {
        echo "Pedido eliminado correctamente.";
    } else {
        echo "Error al eliminar el pedido.";
    }
}

// Redirigir a la página principal
header("Location: index.php");
