<?php
// Conexión a la base de datos
$host = "b1xbvdktlo20sdr39wbi-mysql.services.clever-cloud.com";
$usuario = "udqgmhwed2gjzprz";
$contrasena = "5pDRSAyLkyoXQW28HNBK";
$baseDatos = "b1xbvdktlo20sdr39wbi";

$conexion = new mysqli($host, $usuario, $contrasena, $baseDatos);
if ($conexion->connect_error) {
  die("Error de conexión: " . $conexion->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Recibir y validar los datos
  $nombre     = $_POST['nombre_reg'] ?? '';
  $apellido   = $_POST['apellido_reg'] ?? '';
  $codigo = str_replace('+', '', $_POST['cod_region'] ?? '');
  $celular    = $_POST['celular_reg'] ?? '';
  $telefono   = $codigo . $celular;
  $correo     = $_POST['correo_reg'] ?? '';
  $clave1     = $_POST['contraseña_reg'] ?? '';
  $clave2     = $_POST['confirmar_contraseña_reg'] ?? '';
  $terminos   = isset($_POST['terminos_reg']) ? 1 : 0;

  if (!$terminos) {
    echo "Debe aceptar los términos y condiciones.";
    exit;
  }

  if ($clave1 !== $clave2) {
    echo "Las contraseñas no coinciden.";
    exit;
  }

  if ($codigo === 'none') {
    echo "Debe seleccionar un código de región válido.";
    exit;
  }


  // Encriptar la contraseña (opcional pero recomendado)
  $claveHash = password_hash($clave1, PASSWORD_DEFAULT);

  // Insertar en la tabla usuarios
  $sql = "INSERT INTO usuarios (nombre, apellido, telefono, correo, contraseña)
            VALUES (?, ?, ?, ?, ?)";

  $stmt = $conexion->prepare($sql);
  $stmt->bind_param("sssss", $nombre, $apellido, $telefono, $correo, $claveHash);

  if ($stmt->execute()) {
    echo "<script>alert('Registro exitoso.'); window.location.href='selec_usuario.html';</script>";
  } else {
    echo "Error al registrar: " . $stmt->error;
  }

  $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style.css" />
  <title>Registrarse</title>
</head>

<body>
  <header>
    <div><a href="#">Juli's</a></div>
  </header>
  <div class="espacio"></div>
  <div class="espacio"></div>
  <section class="formulario_reg">
    <div class="container">
      <h1>CREAR PERFIL</h1>
      <form class="form-group" action="registrarse.php" method="post">
        <input
          class="input"
          type="text"
          name="nombre_reg"
          placeholder="Nombre*"
          required />

        <input
          class="input"
          type="text"
          name="apellido_reg"
          placeholder="Apellido*"
          required />

        <div class="telefono_reg">
          <select class="select1" name="cod_region" id="cod_region_reg" required>
            <option value="">Cod. Región*</option>
            <option value="+54">+54</option>
            <option value="+591">+591</option>
            <option value="+56">+56</option>
            <option value="+57">+57</option>
            <option value="+593">+593</option>
            <option value="+595">+595</option>
            <option value="+51">+51</option>
            <option value="+598">+598</option>
            <option value="+58">+58</option>
          </select>
          <input
            class="input"
            type="number"
            name="celular_reg"
            placeholder="Celular*" />
        </div>
        <input
          class="input"
          type="email"
          name="correo_reg"
          placeholder="Correo*"
          required />
        <input
          class="input"
          type="password"
          name="contraseña_reg"
          placeholder="Contraseña*"
          required />
        <input
          class="input"
          type="password"
          name="confirmar_contraseña_reg"
          placeholder="Confirmar contraseña*"
          required />
        <div class="terminos_reg">
          <input type="checkbox" name="terminos_reg" id="terminos_reg" />
          <label for="terminos_reg">Acepto los terminos y condiciones</label>
        </div>
        <div class="boton_reg">
          <a href="selec_usuario.html"><button type="button" class="btn">BACK</button></a>
          <button class="btn" type="submit">REGISTRARSE</button>
        </div>
      </form>
    </div>
  </section>
</body>
<div class="espacio"></div>
<footer>
  <p>&copy; 2023 Juli's. Todos los derechos reservados.</p>
  <p>Desarrollado por Julián</p>
</footer>

</html>