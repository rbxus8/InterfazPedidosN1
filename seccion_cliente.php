<?php

session_start();

$host = "b1xbvdktlo20sdr39wbi-mysql.services.clever-cloud.com";
$user = "udqgmhwed2gjzprz";
$password = "5pDRSAyLkyoXQW28HNBK";
$bd = "b1xbvdktlo20sdr39wbi";

$conexion = new mysqli($host, $user, $password, $bd);


if ($conexion->connect_error) {
  die("error de conexion:" . $conexion->connect_error);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Clientes</title>
  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <header>
    <div>
      <a href="#" style="color: white; text-decoration: none">Juli's</a>
      <a href="s">icon</a>
    </div>
  </header>
  <div class="espacio"></div>

  <section class="container">
    <div>
      <p>Hola (nombre), bienvenido al sistema de solicitud de pedidos</p>
    </div>
    <div class="form-group">
      <a href="crear_pedido.php" class="btn">Agregar Nuevo Pedido</a>
    </div>
    <h1>Pedidos</h1>
    <table>
      <thead>
        <tr>
          <th>Id pedido</th>
          <th>Id local</th>
          <th>Fecha_pedido</th>
          <th>Estado</th>
        </tr>
      </thead>
      <?php
      $sql = "SELECT * from pedidos";
      $result = mysqli_query($conexion, $sql);
      while ($mostrar = mysqli_fetch_array($result)) {

      ?>
        <tbody>
          <tr>
            <td><?php echo $mostrar['id_pedido'] ?></td>
            <td><?php echo $mostrar['id_local'] ?></td>
            <td><?php echo $mostrar['fecha_pedido'] ?></td>
            <td><?php echo $mostrar['estado'] ?></td>
          </tr>
        <?php
      }
        ?>
        </tbody>
    </table>
    <div class="formulario">
      <form action="" class="form-group">
        <div class="form-group">
          <label for="local">Seleccionar local</label>
          <select name="" id=""></select>
        </div>
      </form>
    </div>
  </section>

  <div class="espacio"></div>
  <footer>
    <p>&copy; 2023 Juli's. Todos los derechos reservados.</p>
    <p>Desarrollado por Julián</p>
  </footer>
</body>

</html>