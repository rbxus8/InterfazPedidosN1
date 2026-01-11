<?php
session_start();
header('Content-Type: application/json');

include 'conexion/conexion.php';

$correo   = $_POST['correo']   ?? '';
$password = $_POST['password'] ?? '';

if ($correo === '' || $password === '') {
  echo json_encode([
    'success' => false,
    'message' => 'Campos incompletos'
  ]);
  exit;
}


$stmt = $conexion->prepare("
  SELECT id, nombre, password, tipo_usuario, nivel_acceso
  FROM usuarios
  WHERE correo = ?
");

$stmt->bind_param("s", $correo);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows !== 1) {
  echo json_encode([
    'success' => false,
    'message' => 'Correo no registrado'
  ]);
  exit;
}

$stmt->bind_result($id, $nombre, $hash, $tipo_usuario, $nivel_acceso);
$stmt->fetch();

/* 🔐 VALIDAR PASSWORD */
if (empty($hash) || !password_verify($password, $hash)) {
  echo json_encode([
    'success' => false,
    'message' => 'Contraseña incorrecta'
  ]);
  exit;
}


/* ✅ SESIÓN */
$_SESSION['id_usuario']   = $id;
$_SESSION['nombre']       = $nombre;
$_SESSION['correo']       = $correo;
$_SESSION['tipo_usuario'] = $tipo_usuario;
$_SESSION['nivel_acceso'] = $nivel_acceso;
// Bandera para mostrar mensaje de bienvenida tras login
$_SESSION['login_exitoso'] = true;

echo json_encode(['success' => true]);
