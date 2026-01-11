<?php
session_start();
header('Content-Type: application/json');

include 'conexion/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(["success" => false, "error" => "No autorizado"]);
    exit;
}

$carrito = json_decode(file_get_contents("php://input"), true);

if (!$carrito || count($carrito) === 0) {
    echo json_encode(["success" => false, "error" => "Carrito vacío"]);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_local = $_SESSION['id_local'] ?? 1;

$conexion->begin_transaction();

try {

    // 1️⃣ Crear pedido
    $stmt = $conexion->prepare("
        INSERT INTO pedidos (id_usuario, id_local, estado)
        VALUES (?, ?, 'pendiente')
    ");
    $stmt->bind_param("ii", $id_usuario, $id_local);
    $stmt->execute();

    $id_pedido = $stmt->insert_id;

    // 2️⃣ Detalle pedido
    $detalle = $conexion->prepare("
        INSERT INTO detalle_pedido 
        (id_pedido, id_producto, cantidad, precio)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($carrito as $item) {
        $detalle->bind_param(
            "iiid",
            $id_pedido,
            $item['id'],       // 🔴 OJO AQUÍ
            $item['cantidad'],
            $item['precio']
        );
        $detalle->execute();
    }

    $conexion->commit();

    echo json_encode([
        "success" => true,
        "id_pedido" => $id_pedido
    ]);

} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode([
        "success" => false,
        "error" => "Error al crear pedido"
    ]);
}
