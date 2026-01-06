<?php
include 'conexion/conexion.php';

session_start();

if (session_status() === PHP_SESSION_NONE || session_status() === PHP_SESSION_DISABLED) {
    header("Location: login.php");
    exit;
}

// Obtener datos del formulario
$idCliente = intval($_POST['id_cliente']);
$idLocal   = intval($_POST['id_local']);

// Insertar nuevo pedido
$consultaPedido = "INSERT INTO pedidos (id_cliente, id_local, fecha_pedido, estado) 
                   VALUES (?, ?, NOW(), 'pendiente')";
$stmtPedido = $conexion->prepare($consultaPedido);
$stmtPedido->bind_param("ii", $idCliente, $idLocal);
$stmtPedido->execute();
$idPedido = $stmtPedido->insert_id; // ID del pedido recién creado

// Insertar productos en historial y actualizar stock
if (isset($_POST['productos'])) {
    foreach ($_POST['productos'] as $productoData) {
        $idProducto = intval($productoData['id_producto']);
        $cantidad   = intval($productoData['cantidad']);

        // Validar ID y cantidad
        if ($idProducto <= 0 || $cantidad <= 0) {
            continue; // Ignorar productos inválidos
        }

        // Validar que el producto exista en la tabla productos
        $verificar = $conexion->prepare("SELECT id_producto FROM productos WHERE id_producto = ?");
        $verificar->bind_param("i", $idProducto);
        $verificar->execute();
        $verificar->store_result();
        if ($verificar->num_rows == 0) continue; // Ignorar si no existe


        // Insertar en historial_productos
        $consultaProducto = "
    INSERT INTO historial_productos 
    (id_pedido, id_producto, accion, fecha)
    VALUES (?, ?, ?, NOW())
";

        $stmtProducto = $conexion->prepare($consultaProducto);
        $stmtProducto->bind_param("iii", $idPedido, $idProducto, $cantidad);
        $stmtProducto->execute();


        // Actualizar stock en bodega
        $consultaStock = "UPDATE bodega SET cantidad_producto = cantidad_producto - ? 
                          WHERE id_producto = ? AND id_local = ?";
        $stmtStock = $conexion->prepare($consultaStock);
        $stmtStock->bind_param("iii", $cantidad, $idProducto, $idLocal);
        $stmtStock->execute();
    }
}

// Redirigir con mensaje de éxito
header("Location: index.php?mensaje=Pedido creado correctamente");
exit();
