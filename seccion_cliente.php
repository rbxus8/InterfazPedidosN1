<?php
include 'conexion/conexion.php';
session_start();


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Clientes</title>
  <link rel="stylesheet" href="css/style.css" />
</head>

<div class="cart-panel" id="cartPanel">
  <h2>Carrito</h2>
  <ul id="cartItems"></ul>
  <h3>Total: $<span id="cartTotal">0</span></h3>
  <button id="checkoutBtn">Finalizar pedido</button>
  <button id="closeCart">Cerrar</button>
</div>


<body class="body_cliente">
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
    <button class="user">
      <img src="img/user_icon.png" alt="User Icon" style="height: 1.5em;">
    </button>
    <!-- Botón Carrito fijo -->
    <button class="cart-btn" id="cartBtn">
      <img src="https://cdn-icons-png.flaticon.com/512/107/107831.png" style="height: 22px;">
      <span id="cartCount">0</span>
    </button>

  </header>

  <main class="main_cliente">
    <section class="portada_cliente">
      <div>
        <img src="img/Post_de_Instagram_Vertical_Frase_Acuarela_Azul-removebg-preview.png" alt="">
      </div>
    </section>
    <section class="container_saludo">
      <span>
        <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre'] ?? ''); ?>!</h1>
        <p>​​​Los pedidos que realices en esta web los entregaremos al segundo día después de hacer la compra o en la fecha indicada. Ten en cuenta que tu pedido no será entregado hoy ni mañana, recibirás tus delicias en dos días hábiles.</p>
      </span>
    </section>
    <section class="container_productos">
      <h1>Productos</h1>
      <h3>Elige tus productos favoritos y agrégales al carrito de compras.</h3>
      <div class="categoria_productos">
        <button type="button" onclick="mostrar('bebidas')" class="btn">Bebidas</button>
        <button type="button" onclick="mostrar('postres')" class="btn">Postres</button>
        <button type="button" onclick="mostrar('todos')" class="btn">Todos</button>
      </div>

      <div class="productos_cards">
        <div class="card item bebidas">
          <img src="img/bebida_de_fresas.jpg" alt="Bebida de Fresas">
          <h3>Bebida de fresas</h3>
          <p>Deliciosa bebida de fresas naturales.</p>
          <span>$1500</span>
          <button class="btn">Comprar</button>
        </div>
        <div class="card item bebidas">
          <img src="img/empanada_de_carne.jpg" alt="Empanada de Carne">
          <h3>Tea</h3>
          <p>Deliciosa bebida de té helado con limón.</p>
          <span>$1500</span>
          <button class="btn">Comprar</button>
        </div>
        <div class="card item postres">
          <img src="img/postres_de_chocolate.jpg" alt="Postres de Chocolate">
          <h3>Postres de chocolate</h3>
          <p>Deliciosos postres de chocolate con un toque especial.</p>
          <span>$1500</span>
          <button class="btn">Comprar</button>
        </div>
        <div class="card item postres">
          <img src="img/postres_de_chocolate.jpg" alt="Postres de Chocolate">
          <h3>Postres de chocolate</h3>
          <p>Deliciosos postres de chocolate con un toque especial.</p>
          <span>$1500</span>
          <button class="btn">Comprar</button>
        </div>
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