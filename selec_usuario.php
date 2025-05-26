<?php
session_start();

include 'conexion.php';

// Inicializar variables para mensajes de error
$error_correo = '';
$error_contrasena = '';



// Procesar inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $correo = $_POST['correo'];
  $contraseña = $_POST['contraseña'];

  // Preparar y ejecutar la consulta para obtener la contraseña del usuario
  $stmt = $conexion->prepare("SELECT contraseña FROM usuarios WHERE correo = ?");
  $stmt->bind_param("s", $correo);
  $stmt->execute();
  $stmt->store_result(); // Almacenar el resultado

  // Verificar si el usuario existe
  if ($stmt->num_rows === 1) {
    $stmt->bind_result($hash);
    $stmt->fetch();

    // Verificar la contraseña
    if (password_verify($contraseña, $hash)) {
      // Si la contraseña es correcta, redirigir según el dominio del correo
      if (str_ends_with($correo, '@pan.com')) {
        header("Location: index.php"); // Redirige a la página especial
      } else {
        header("Location: seccion_cliente.html"); // Redirige a la página normal
      }
      exit();
    } else {
      // Si la contraseña es incorrecta
      $error_contrasena = "Contraseña incorrecta";
    }
  } else {
    // Si el correo no está registrado
    $error_correo = "Correo no registrado";
  }

  // Cerrar la declaración
  $stmt->close();
}

// Cerrar la conexión a la base de datos
$conexion->close();
?>




<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User_select</title>
  <link rel="stylesheet" href="style.css" />
</head>

<body class="body_inicio">
  <header>
    <div><a href="#">Juli's</a></div>
  </header>
  <div class="espacio"></div>
  <section class="container_usuario">
    <div class="img_inicio">
      <img
        src="img/Carrito de Compras GoShop.png"
        alt="Imagen de sitio de compras_goshop"
        style="width: 10em; height: 10em" />
    </div>

    <div class="cuerpo_form">
      <h5 style="color: #64748b">Iniciar sesión</h5>
      <form action="#" method="post">
        <input
          class="input <?php if (!empty($error_correo)) echo 'input-error'; ?>"
          type="email"
          name="correo"
          placeholder="Correo electrónico"
          value="<?php echo htmlspecialchars($_POST['correo'] ?? ''); ?>"
          required />
        <?php if (!empty($error_correo)): ?>
          <p class="error-message"><?php echo $error_correo; ?></p>
        <?php endif; ?>

        <input
          class="input <?php if (!empty($error_contrasena)) echo 'input-error'; ?>"
          type="password"
          name="contraseña"
          placeholder="Contraseña"
          required />
        <?php if (!empty($error_contrasena)): ?>
          <p class="error-message"><?php echo $error_contrasena; ?></p>
        <?php endif; ?>

        <button class="btn" type="submit">Entrar</button>
      </form>
    </div>

    <div class="espacio"></div>
    <div class="sesion_ops">
      <a href="#">Recuperar cuenta</a>
      <a href="registrarse.php">Registrar cuenta</a>
    </div>
  </section>
  <div class="espacio"></div>
  <footer>
    <p>&copy; 2023 Juli's. Todos los derechos reservados.</p>
    <p>Desarrollado por Julián</p>
  </footer>

</body>

</html>