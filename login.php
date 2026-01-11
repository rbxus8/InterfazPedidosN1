<?php
session_start();
include 'conexion/conexion.php';

$error_correo = '';
$error_contrasena = '';
$mensaje_exito = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $correo   = $_POST['correo'] ?? '';
  $password = $_POST['password'] ?? '';

  if ($correo === '' || $password === '') {
    $error_contrasena = "Campos incompletos";
  } else {

    $stmt = $conexion->prepare("
      SELECT id, nombre, password, tipo_usuario, nivel_acceso
      FROM usuarios
      WHERE correo = ?
    ");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {

      $stmt->bind_result($id, $nombre, $hash, $tipo_usuario, $nivel_acceso);
      $stmt->fetch();

      if (!empty($hash) && password_verify($password, $hash)) {

        $_SESSION['id_usuario']   = $id;
        $_SESSION['nombre']       = $nombre;
        $_SESSION['correo']       = $correo;
        $_SESSION['tipo_usuario'] = $tipo_usuario;
        $_SESSION['nivel_acceso'] = $nivel_acceso;
        $mensaje_exito = "¡Inicio de sesión exitoso! Redirigiendo...";
        echo "<div style='color: green; font-weight: bold;'>$mensaje_exito</div>";
        // Redirigir después de 2 segundos
        if ($tipo_usuario === 'empleado') {
          echo "<script>setTimeout(function(){ window.location.href = '" . ($nivel_acceso === 'admin' ? "admin/dashboard.php" : "vendedor/pedidos.php") . "'; }, 2000);</script>";
        } else {
          echo "<script>setTimeout(function(){ window.location.href = 'tienda.php'; }, 2000);</script>";
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
}

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <h2>Iniciar sesión</h2>
  <?php if ($error_correo): ?>
    <div style="color: red;"> <?= $error_correo ?> </div>
  <?php endif; ?>
  <?php if ($error_contrasena): ?>
    <div style="color: red;"> <?= $error_contrasena ?> </div>
  <?php endif; ?>
  <form method="post" action="">
    <label for="correo">Correo:</label>
    <input type="email" name="correo" id="correo" required><br><br>
    <label for="password">Contraseña:</label>
    <input type="password" name="password" id="password" required><br><br>
    <button type="submit">Ingresar</button>
  </form>
</body>

</html>