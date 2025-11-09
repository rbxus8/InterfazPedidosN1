<?php
include 'conexion/conexion.php';
session_start();

/* 🔹 Consulta de productos directamente desde la tabla */
$consultaProductos = "
SELECT 
    id_producto,
    nombre,
    precio,
    unidad_medida,
    categoria
FROM productos
WHERE estado = 'disponible'
ORDER BY categoria, nombre
";

$resultadoProductos = $conexion->query($consultaProductos);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Clientes</title>
  <link rel="stylesheet" href="css/style.css" />
</head>

<!-- 🛒 Panel del carrito -->
<div class="cart-panel" id="cartPanel">
  <h2>Carrito</h2>
  <ul id="cartItems"></ul>
  <h3>Total: $<span id="cartTotal">0</span></h3>
  <button id="checkoutBtn">Finalizar pedido</button>
  <button id="closeCart">Cerrar</button>
</div>

<body class="body_cliente">
  <header class="header">
    <a href="#"><img src="img/iconosinfondotitulo.png" alt="nombre_icon_goshop" style="height: 1.5em;"></a>
    <button onclick="cambiarColorTema()" class="chance_color" id="chance_color">🌞</button>
    <button class="user">
      <img src="img/user_icon.png" alt="User Icon" style="height: 1.5em;">
    </button>
    <!-- Botón Carrito -->
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
        <p>Los pedidos que realices en esta web los entregaremos al segundo día después de hacer la compra o en la fecha indicada.</p>
      </span>
    </section>

    <section class="container_productos">
      <h1>Productos</h1>
      <h3>Elige tus productos favoritos y agrégalos al carrito de compras.</h3>

      <!-- Botones de filtro -->
      <div class="categoria_productos">
        <button type="button" onclick="mostrar('bebidas')" class="btn">Bebidas</button>
        <button type="button" onclick="mostrar('postres')" class="btn">Postres</button>
        <button type="button" onclick="mostrar('todos')" class="btn">Todos</button>
      </div>

      <!-- 🔹 Productos desde base de datos -->
      <div class="productos_cards">
        <?php while ($producto = $resultadoProductos->fetch_assoc()): ?>
          <?php
          $categoria = strtolower(trim($producto['categoria']));
          $claseCategoria = ($categoria === 'bebidas' || $categoria === 'bebida') ? 'bebidas' : 'postres';
          ?>
          <div class="card item <?= $claseCategoria ?>">
            <img src="img/<?= $claseCategoria ?>.jpg" alt="<?= htmlspecialchars($producto['nombre']) ?>">
            <h3><?= htmlspecialchars($producto['nombre']) ?></h3>
            <p><?= ucfirst($categoria) ?></p>
            <span>$<?= number_format($producto['precio'], 2, ',', '.') ?></span>
            <button class="btn add-to-cart"
              data-id="<?= $producto['id_producto'] ?>"
              data-nombre="<?= htmlspecialchars($producto['nombre']) ?>"
              data-precio="<?= $producto['precio'] ?>">
              Comprar
            </button>
          </div>
        <?php endwhile; ?>
      </div>
    </section>
  </main>

  <footer class="footer">
    <p>&copy; 2025 Juli's. Todos los derechos reservados.</p>
    <p>Desarrollado por Julián</p>
  </footer>

  <script src="js/script.js"></script>

  <script>
    // 🧃 Filtrar productos
    function mostrar(categoria) {
      const items = document.querySelectorAll('.item');
      items.forEach(item => {
        if (categoria === 'todos' || item.classList.contains(categoria)) {
          item.style.display = 'block';
        } else {
          item.style.display = 'none';
        }
      });
    }

    // 🛒 Carrito simple
    const cart = [];
    const cartPanel = document.getElementById('cartPanel');
    const cartBtn = document.getElementById('cartBtn');
    const closeCart = document.getElementById('closeCart');
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    const cartCount = document.getElementById('cartCount');

    cartBtn.addEventListener('click', () => cartPanel.style.display = 'block');
    closeCart.addEventListener('click', () => cartPanel.style.display = 'none');

    document.querySelectorAll('.add-to-cart').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        const nombre = btn.dataset.nombre;
        const precio = parseFloat(btn.dataset.precio);
        const item = cart.find(p => p.id === id);

        if (item) {
          item.cantidad++;
        } else {
          cart.push({
            id,
            nombre,
            precio,
            cantidad: 1
          });
        }

        actualizarCarrito();
      });
    });

    function actualizarCarrito() {
      cartItems.innerHTML = '';
      let total = 0;
      cart.forEach(item => {
        total += item.precio * item.cantidad;
        const li = document.createElement('li');
        li.textContent = `${item.nombre} x${item.cantidad} - $${(item.precio * item.cantidad).toFixed(2)}`;
        cartItems.appendChild(li);
      });
      cartTotal.textContent = total.toFixed(2);
      cartCount.textContent = cart.reduce((a, b) => a + b.cantidad, 0);
    }
  </script>
</body>

</html>