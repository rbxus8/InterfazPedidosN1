<?php

session_start();

include 'conexion.php';
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
  <header class="header">
    <a href="#"><img
        src="img/Carrito_de_Compras.png"
        alt="nombre_icon_goshop"
        style="height: 1.5em ; ">
    </a>
    <button onclick="cambiarColorTema()" class="chance_color" id="chance_color">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-brightness-high" viewBox="0 0 16 16">
        <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6m0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8M8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0m0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13m8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5M3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8m10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0m-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0m9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707M4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708" />
      </svg>
    </button>
  </header>

  <main class="main">

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

    <main>

      <footer class="footer">
        <p>&copy; 2023 Juli's. Todos los derechos reservados.</p>
        <p>Desarrollado por Julián</p>
      </footer>
      <script src="script.js"></script>
</body>

</html>