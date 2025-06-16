<?php
// login.php
// Iniciar sesión y regenerar ID de sesión para mayor seguridad
session_start();
session_regenerate_id(true); // Regenerar ID de sesión para mayor seguridad

// Incluir el archivo de conexión a la base de datos
include 'conexion/conexion.php';

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
      if (str_ends_with($correo, '@admin.com')) {
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
  <title>Login</title>
  <link rel="stylesheet" href="css/style.css" />
</head>

<body>
  <header class="header">
    <a href="#"><img
        src="img/iconosinfondotitulo.png"
        alt="nombre_icon_goshop"
        style="height: 1.5em ; ">
    </a>
    <button onclick="cambiarColorTema()" class="chance_color" id="chance_color">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-brightness-high" viewBox="0 0 16 16">
        <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6m0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8M8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0m0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13m8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5M3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8m10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0m-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0m9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707M4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708" />
      </svg>
    </button>
  </header>

  <main class="main" id="main_login">
    <section class="container" id="container">
      <div class="img_inicio">
        <img
          src="img/iconosinfondo.png"
          alt="Imagen de sitio de compras_goshop"
          style="width: 10em; height: 10em" />
      </div>

      <div class="cuerpo_form">
        <h5>Iniciar sesión</h5>
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

      <div class="sesion_ops">
        <a href="#">Recuperar cuenta</a>
        <a href="registrarse.php">Registrar cuenta</a>
      </div>
    </section>
  </main>

  <footer class="footer">
    <p>&copy; 2023 Juli's. Todos los derechos reservados.</p>
    <p>Desarrollado por Julián</p>
  </footer>
  <script src="js/script.js"></script>
</body>

</html>