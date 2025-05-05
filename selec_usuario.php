<?php
$host = "b1xbvdktlo20sdr39wbi-mysql.services.clever-cloud.com";
$usuario = "udqgmhwed2gjzprz";
$contrasena = "5pDRSAyLkyoXQW28HNBK";
$baseDatos = "b1xbvdktlo20sdr39wbi";

// Crear conexión
$conexion = new mysqli($host, $usuario, $contrasena, $baseDatos);

if ($conexion->connect_error) {
  die("Error de conexión: " . $conexion->connect_error);
}

// Procesar inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $correo = $_POST['correo'];
  $contraseña = $_POST['contraseña'];

  // Preparar y ejecutar la consulta
  $stmt = $conexion->prepare("SELECT contraseña FROM usuarios WHERE correo = ?");
  $stmt->bind_param("s", $correo);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows === 1) {
    $stmt->bind_result($hash);
    $stmt->fetch();

    if (password_verify($contraseña, $hash)) {
      header("Location: inicio.php");
      exit();
    } else {
      echo "<script>alert('Contraseña incorrecta');</script>";
    }
  } else {
    echo "<script>alert('Correo no registrado');</script>";
  }

  $stmt->close();
}
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
          class="input"
          type="email"
          name="correo"
          placeholder="Correo electrónico"
          required />
        <input
          class="input"
          type="password"
          name="contraseña"
          placeholder="Contraseña"
          required />
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
  <script src="alert.js"></script>
</body>

</html>