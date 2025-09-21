<?php
include 'conexion/conexion.php';

$idPedido = intval($_POST['id_pedido']);

// 1. Actualizar cantidades de productos ya en el pedido
if (isset($_POST['productos'])) {
    foreach ($_POST['productos'] as $idProducto => $productoData) {
        $cantidadNueva = intval($productoData['cantidad']);

        // Cantidad anterior en historial
        $sql = "SELECT accion FROM historial_productos 
                WHERE id_pedido = ? AND id_producto = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ii", $idPedido, $idProducto);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $cantidadAnterior = intval($row['accion']);

        // Diferencia solicitada
        $diferencia = $cantidadNueva - $cantidadAnterior;

        // Stock disponible
        $sqlStockCheck = "SELECT cantidad_producto FROM bodega WHERE id_producto = ?";
        $stmt = $conexion->prepare($sqlStockCheck);
        $stmt->bind_param("i", $idProducto);
        $stmt->execute();
        $resStock = $stmt->get_result();
        $rowStock = $resStock->fetch_assoc();
        $stockDisponible = intval($rowStock['cantidad_producto']);

        // Si la diferencia es positiva (quiere más), limitar al stock disponible
        if ($diferencia > 0) {
            if ($diferencia > $stockDisponible) {
                $diferencia = $stockDisponible;
            }
            $cantidadNueva = $cantidadAnterior + $diferencia; // Ajusto la nueva cantidad
        }

        // Actualizar historial
        $sqlUpdate = "UPDATE historial_productos 
                      SET accion = ? 
                      WHERE id_pedido = ? AND id_producto = ?";
        $stmt = $conexion->prepare($sqlUpdate);
        $stmt->bind_param("iii", $cantidadNueva, $idPedido, $idProducto);
        $stmt->execute();

        // Ajustar stock (si hay diferencia)
        if ($diferencia != 0) {
            $sqlStock = "UPDATE bodega 
                         SET cantidad_producto = cantidad_producto - ? 
                         WHERE id_producto = ?";
            $stmt = $conexion->prepare($sqlStock);
            $stmt->bind_param("ii", $diferencia, $idProducto);
            $stmt->execute();
        }
    }
}

// 2. Eliminar producto
if (!empty($_POST['eliminar_producto'])) {
    $idProductoEliminar = intval($_POST['eliminar_producto']);

    $sql = "SELECT accion FROM historial_productos 
            WHERE id_pedido = ? AND id_producto = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $idPedido, $idProductoEliminar);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $cantidadEliminar = intval($row['accion']);

    // Devolver stock
    $sqlStock = "UPDATE bodega 
                 SET cantidad_producto = cantidad_producto + ? 
                 WHERE id_producto = ?";
    $stmt = $conexion->prepare($sqlStock);
    $stmt->bind_param("ii", $cantidadEliminar, $idProductoEliminar);
    $stmt->execute();

    // Eliminar historial
    $sqlDel = "DELETE FROM historial_productos 
               WHERE id_pedido = ? AND id_producto = ?";
    $stmt = $conexion->prepare($sqlDel);
    $stmt->bind_param("ii", $idPedido, $idProductoEliminar);
    $stmt->execute();

    // Si no quedan productos, eliminar pedido
    $check = $conexion->query("SELECT COUNT(*) AS total FROM historial_productos WHERE id_pedido = $idPedido");
    $row = $check->fetch_assoc();
    if ($row['total'] == 0) {
        $conexion->query("DELETE FROM pedidos WHERE id_pedido = $idPedido");
        header("Location: index.php?mensaje=Pedido eliminado (sin productos)");
        exit();
    }
}

// 3. Insertar o actualizar nuevos productos
if (!empty($_POST['nuevos_productos'])) {
    foreach ($_POST['nuevos_productos'] as $idProducto => $productoData) {
        if (isset($productoData['seleccionado']) && intval($productoData['cantidad']) > 0) {
            $cantidadNueva = intval($productoData['cantidad']);

            // Verificar stock disponible
            $sqlStockCheck = "SELECT cantidad_producto FROM bodega WHERE id_producto = ?";
            $stmt = $conexion->prepare($sqlStockCheck);
            $stmt->bind_param("i", $idProducto);
            $stmt->execute();
            $resStock = $stmt->get_result();
            $rowStock = $resStock->fetch_assoc();
            $stockDisponible = intval($rowStock['cantidad_producto']);

            if ($cantidadNueva > $stockDisponible) {
                $cantidadNueva = $stockDisponible;
            }

            if ($cantidadNueva > 0) {
                // Verificar si el producto ya existe en el pedido
                $sqlCheck = "SELECT accion FROM historial_productos WHERE id_pedido = ? AND id_producto = ?";
                $stmt = $conexion->prepare($sqlCheck);
                $stmt->bind_param("ii", $idPedido, $idProducto);
                $stmt->execute();
                $resCheck = $stmt->get_result();

                if ($resCheck->num_rows > 0) {
                    // Ya existe → sumamos cantidades
                    $row = $resCheck->fetch_assoc();
                    $cantidadTotal = $row['accion'] + $cantidadNueva;

                    $sqlUpdate = "UPDATE historial_productos 
                                  SET accion = ? 
                                  WHERE id_pedido = ? AND id_producto = ?";
                    $stmt = $conexion->prepare($sqlUpdate);
                    $stmt->bind_param("iii", $cantidadTotal, $idPedido, $idProducto);
                    $stmt->execute();
                } else {
                    // No existe → lo insertamos
                    $sqlInsert = "INSERT INTO historial_productos (id_pedido, id_producto, accion) 
                                  VALUES (?, ?, ?)";
                    $stmt = $conexion->prepare($sqlInsert);
                    $stmt->bind_param("iii", $idPedido, $idProducto, $cantidadNueva);
                    $stmt->execute();
                }

                // Descontar stock
                $sqlStock = "UPDATE bodega 
                             SET cantidad_producto = cantidad_producto - ? 
                             WHERE id_producto = ?";
                $stmt = $conexion->prepare($sqlStock);
                $stmt->bind_param("ii", $cantidadNueva, $idProducto);
                $stmt->execute();
            }
        }
    }
}


// 4. Estado del pedido
if (isset($_POST['estado'])) {
    $estado = $_POST['estado'];
    $sqlEstado = "UPDATE pedidos SET estado = ? WHERE id_pedido = ?";
    $stmt = $conexion->prepare($sqlEstado);
    $stmt->bind_param("si", $estado, $idPedido);
    $stmt->execute();
}

header("Location: editar_pedido.php?id=$idPedido&mensaje=Pedido actualizado correctamente");
exit();
