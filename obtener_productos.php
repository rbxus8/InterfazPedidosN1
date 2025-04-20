<?php
$host = "b1xbvdktlo20sdr39wbi-mysql.services.clever-cloud.com";
$usuario = "udqgmhwed2gjzprz";         // Cambia esto por el usuario real
$contrasena = "5pDRSAyLkyoXQW28HNBK";   // Nunca publiques esto en Git o público
$baseDatos = "b1xbvdktlo20sdr39wbi"; // Ej: b1xbvdktlo20sdr39wbi

// Crear conexión
$conexion = new mysqli($host, $usuario, $contrasena, $baseDatos);

if ($conexion->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos.']);
    exit;
}

// Obtener el ID del local
$idLocal = isset($_GET['local']) ? intval($_GET['local']) : 0;

if ($idLocal > 0) {
    $sql = "
        SELECT productos.id_producto, productos.nombre, bodega.cantidad_producto 
        FROM productos
        INNER JOIN bodega ON productos.id_producto = bodega.id_producto
        WHERE bodega.id_local = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $idLocal);
    $stmt->execute();
    $result = $stmt->get_result();

    $productos = [];
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }

    echo json_encode(['success' => true, 'productos' => $productos]);
} else {
    echo json_encode(['success' => false, 'message' => 'Local no válido.']);
}
?>


