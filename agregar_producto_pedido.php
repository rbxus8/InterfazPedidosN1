<?php
$host = "b1xbvdktlo20sdr39wbi-mysql.services.clever-cloud.com";
$usuario = "udqgmhwed2gjzprz";         // Cambia esto por el usuario real
$contrasena = "5pDRSAyLkyoXQW28HNBK";   // Nunca publiques esto en Git o público
$baseDatos = "b1xbvdktlo20sdr39wbi"; // Ej: b1xbvdktlo20sdr39wbi

// Crear conexión
$conexion = new mysqli($host, $usuario, $contrasena, $baseDatos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener datos del formulario
$idProducto = $_POST['id_producto'];
$cantidad = $_POST['cantidad'];
$idPedido = $_POST['id_pedido'];  // Suponemos que el ID del pedido también se pasa desde el formulario

// Verificar si el producto ya está en el pedido
$verificarProducto = "SELECT * FROM historial_productos WHERE id_pedido = ? AND id_producto = ?";
$stmtVerificar = $conexion->prepare($verificarProducto);
$stmtVerificar->bind_param("ii", $idPedido, $idProducto);
$stmtVerificar->execute();
$resultado = $stmtVerificar->get_result();

if ($resultado->num_rows > 0) {
    // Si el producto ya está en el pedido, actualizar la cantidad
    $actualizarProducto = "UPDATE historial_productos SET accion = accion + ? WHERE id_pedido = ? AND id_producto = ?";
    $stmtActualizar = $conexion->prepare($actualizarProducto);
    $stmtActualizar->bind_param("iii", $cantidad, $idPedido, $idProducto);
    
    if ($stmtActualizar->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el producto en el pedido.']);
    }
} else {
    // Si el producto no está en el pedido, agregarlo
    $agregarProducto = "INSERT INTO historial_productos (id_pedido, id_producto, accion, fecha) VALUES (?, ?, ?, NOW())";
    $stmtAgregar = $conexion->prepare($agregarProducto);
    $stmtAgregar->bind_param("iii", $idPedido, $idProducto, $cantidad);
    
    if ($stmtAgregar->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al agregar el producto al pedido.']);
    }
}

$conexion->close();
?>

