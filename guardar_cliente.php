<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "uwu");

if ($conexion->connect_error) {
    die(json_encode(["success" => false, "message" => "Error de conexión a la base de datos."]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente = $_POST['cliente'];
    $local = $_POST['local'];
    $productos = $_POST['productos'];

    // Crear un nuevo pedido
    $stmt = $conexion->prepare("INSERT INTO pedidos (id_cliente, id_local, estado, fecha_pedido) VALUES (?, ?, 'pendiente', NOW())");
    $stmt->bind_param("ii", $cliente, $local);
    $stmt->execute();
    $idPedido = $conexion->insert_id;

    // Insertar productos seleccionados en historial_productos y actualizar stock
    foreach ($productos as $idProducto => $productoData) {
        if (isset($productoData['seleccionado']) && $productoData['seleccionado'] == 1) {
            $cantidad = intval($productoData['cantidad']);

            // Verificar si hay suficiente stock
            $stmt = $conexion->prepare("SELECT cantidad_producto FROM bodega WHERE id_producto = ? AND id_local = ?");
            $stmt->bind_param("ii", $idProducto, $local);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row && $row['cantidad_producto'] >= $cantidad) {
                // Insertar producto en historial_productos
                $stmt = $conexion->prepare("INSERT INTO historial_productos (id_pedido, id_producto, accion, fecha) VALUES (?, ?, ?, NOW())");
                $stmt->bind_param("iii", $idPedido, $idProducto, $cantidad);
                $stmt->execute();

                // Actualizar el stock en bodega
                $nuevoStock = $row['cantidad_producto'] - $cantidad;
                $stmt = $conexion->prepare("UPDATE bodega SET cantidad_producto = ? WHERE id_producto = ? AND id_local = ?");
                $stmt->bind_param("iii", $nuevoStock, $idProducto, $local);
                $stmt->execute();
            } else {
                echo json_encode(["success" => false, "message" => "No hay suficiente stock para el producto con ID: $idProducto"]);
                exit;
            }
        }
    }

    // Respuesta exitosa
    echo json_encode(["success" => true, "message" => "Pedido creado correctamente."]);
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}
?>

