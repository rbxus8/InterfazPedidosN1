<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Usuario no autenticado'
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$_SESSION['carrito'] = $data;

echo json_encode([
    'success' => true
]);
