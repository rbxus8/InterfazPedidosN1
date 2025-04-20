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
?>
