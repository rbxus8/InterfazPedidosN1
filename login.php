<?php

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

      // 🔐 SESIONES PRINCIPALES
      $_SESSION['id_usuario']   = $id;
      $_SESSION['nombre']       = $nombre;
      $_SESSION['correo']       = $correo;
      $_SESSION['tipo_usuario'] = $tipo_usuario;
      $_SESSION['nivel_acceso'] = $nivel_acceso;

      // 🔁 REDIRECCIÓN SEGÚN TIPO
      if ($tipo_usuario === 'empleado') {

        if ($nivel_acceso === 'admin') {
          header("Location: admin/dashboard.php");
        } else {
          header("Location: vendedor/pedidos.php");
        }
      } else {
        // 👤 CLIENTE
        header("Location: tienda.php");
      }

      exit;


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
