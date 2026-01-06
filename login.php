<?php
session_start();
session_regenerate_id(true);

include 'conexion/conexion.php';

$error_correo = '';
$error_contrasena = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $correo = $_POST['correo'];
  $contraseña = $_POST['contraseña'];

  $stmt = $conexion->prepare("
        SELECT id, nombre, contraseña, tipo_usuario, nivel_acceso
        FROM usuarios
        WHERE correo = ?
    ");
  $stmt->bind_param("s", $correo);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows === 1) {

    $stmt->bind_result(
      $id,
      $nombre,
      $hash,
      $tipo_usuario,
      $nivel_acceso
    );
    $stmt->fetch();

    if (password_verify($contraseña, $hash)) {

      // 🔐 SESIÓN BASE
      $_SESSION['id_usuario'] = $id;
      $_SESSION['nombre']     = $nombre;
      $_SESSION['correo']     = $correo;

      // 🧠 Detectar correo corporativo
      $es_corporativo = str_ends_with($correo, '@empresa.com');

      if ($es_corporativo) {

        // 🔑 PERSONAL INTERNO
        if ($nivel_acceso === 'admin') {
          $_SESSION['rol'] = 'admin';
          header("Location: admin/dashboard.php");
        } else {
          $_SESSION['rol'] = 'vendedor';
          header("Location: vendedor/pedidos.php");
        }
      } else {

        // 👤 CLIENTE NORMAL
        $_SESSION['rol'] = 'cliente';
        header("Location: tienda.php");
      }

      exit;
    } else {
      $error_contrasena = "Contraseña incorrecta";
    }
  } else {
    $error_correo = "Correo no registrado";
  }

  $stmt->close();
}

$conexion->close();
