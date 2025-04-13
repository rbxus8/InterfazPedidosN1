<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "uwu");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener datos del producto y pedido
$idPedido = intval($_POST['id_pedido']);
$idProducto = intval($_POST['id_producto']);

try {
    // Eliminar el producto del pedido
    $consultaEliminar = "DELETE FROM historial_productos WHERE id_pedido = ? AND id_producto = ?";
    $stmtEliminar = $conexion->prepare($consultaEliminar);
    $stmtEliminar->bind_param("ii", $idPedido, $idProducto);

    if (!$stmtEliminar->execute()) {
        throw new Exception("Error al eliminar el producto.");
    }

    // Redirigir de vuelta a la página de edición
    header("Location: editar_pedido.php?id=$idPedido&mensaje=Producto eliminado correctamente");
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
