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

if (isset($_POST['id_producto']) && isset($_POST['cantidad'])) {
    $idProducto = $_POST['id_producto'];
    $cantidad = $_POST['cantidad'];

    // Actualizar stock en la tabla bodega
    $sql = "UPDATE bodega SET cantidad_producto = cantidad_producto + ? WHERE id_producto = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $cantidad, $idProducto);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Stock actualizado']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar el stock']);
    }

    $stmt->close();
}

$conexion->close();
?>
