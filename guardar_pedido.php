<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "uwu");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener el ID del cliente y el local desde el formulario
$idCliente = $_POST['cliente'];
$idLocal = $_POST['local'];

// Insertar el nuevo pedido
$consultaPedido = "INSERT INTO pedidos (id_cliente, id_local, fecha_pedido, estado) VALUES (?, ?, NOW(), 'pendiente')";
$stmtPedido = $conexion->prepare($consultaPedido);
$stmtPedido->bind_param("ii", $idCliente, $idLocal);
$stmtPedido->execute();
$idPedido = $stmtPedido->insert_id; // Obtener el ID del pedido recién insertado

// Insertar los productos seleccionados y sus cantidades
if (isset($_POST['productos'])) {
    foreach ($_POST['productos'] as $idProducto => $productoData) {
        $cantidad = intval($productoData['cantidad']); // Obtener la cantidad seleccionada
        // Insertar producto en historial_productos
        $consultaProducto = "INSERT INTO historial_productos (id_pedido, id_producto, accion, fecha) 
                             VALUES (?, ?, ?, NOW())";
        $stmtProducto = $conexion->prepare($consultaProducto);
        $stmtProducto->bind_param("iii", $idPedido, $idProducto, $cantidad);
        $stmtProducto->execute();

        // Descontar la cantidad del stock disponible en la tabla bodega
        $consultaStock = "UPDATE bodega SET cantidad_producto = cantidad_producto - ? 
                          WHERE id_producto = ? AND id_local = ?";
        $stmtStock = $conexion->prepare($consultaStock);
        $stmtStock->bind_param("iii", $cantidad, $idProducto, $idLocal);
        $stmtStock->execute();
    }
}

// Redirigir a la página principal
header("Location: index.php?mensaje=Pedido creado correctamente");
exit();
?>
