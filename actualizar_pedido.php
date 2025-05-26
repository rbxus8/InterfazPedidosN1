<?php
include 'conexion.php';

// Obtener el ID del pedido desde el formulario
$idPedido = $_POST['id_pedido'];  // ID del pedido

// Actualizar la cantidad de productos seleccionados
if (isset($_POST['productos'])) {
    foreach ($_POST['productos'] as $idProducto => $productoData) {
        $cantidad = intval($productoData['cantidad']); // Obtener la cantidad seleccionada

        // Actualizar la cantidad de productos en la tabla historial_productos
        $consulta = "UPDATE historial_productos 
                     SET accion = ? 
                     WHERE id_pedido = ? AND id_producto = ?";
        $stmt = $conexion->prepare($consulta);
        $stmt->bind_param("iii", $cantidad, $idPedido, $idProducto);
        $stmt->execute();
    }
}

// Actualizar el estado del pedido si se cambia
if (isset($_POST['estado'])) {
    $estado = $_POST['estado'];
    $consultaEstado = "UPDATE pedidos SET estado = ? WHERE id_pedido = ?";
    $stmtEstado = $conexion->prepare($consultaEstado);
    $stmtEstado->bind_param("si", $estado, $idPedido);
    $stmtEstado->execute();
}

// Confirmar cambios y redirigir
header("Location: index.php?mensaje=Pedido actualizado correctamente");
exit();
