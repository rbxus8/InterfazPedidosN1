<?php
session_start();

include 'conexion/conexion.php';

/* 🔐 Estado de sesión */
$usuarioLogueado = isset($_SESSION['id_usuario']);

/* 🛒 Permiso para comprar (solo clientes logueados) */
$puedeComprar = (
  isset($_SESSION['id_usuario']) &&
  ($_SESSION['tipo_usuario'] ?? '') === 'cliente'
);

// Mostrar mensaje de éxito si acaba de iniciar sesión
$mensajeBienvenida = '';
if (isset($_SESSION['login_exitoso'])) {
  $mensajeBienvenida = '¡Listo para comprar! Ya puedes usar el carrito.';
  unset($_SESSION['login_exitoso']);
}

// DEBUG: Mostrar contenido de la sesión
if (isset($_GET['debug'])) {
  echo '<pre style="background:#222;color:#0f0;padding:1em;">';
  echo '$_SESSION = ';
  print_r($_SESSION);
  echo '</pre>';
}


/* 🔹 Consulta de productos */
$consultaProductos = "
  SELECT 
    p.id_producto,
    p.nombre,
    p.precio,
    p.unidad_medida,
    c.nombre AS categoria
  FROM productos p
  INNER JOIN categorias c 
    ON p.id_categoria = c.id_categoria
  WHERE p.estado = 'disponible'
  ORDER BY c.nombre, p.nombre
";


$resultadoProductos = $conexion->query($consultaProductos);

/* 🔹 CONSULTA CATEGORÍAS */
$categorias = $conexion->query("
  SELECT id_categoria, nombre 
  FROM categorias 
  ORDER BY nombre
");

if (!$categorias) {
  die("Error en consulta categorias: " . $conexion->error);
}
?>



<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Clientes</title>
  <link rel="stylesheet" href="css/cliente.css" />
</head>

<body class="body_cliente">
  <?php if ($mensajeBienvenida): ?>
    <div style="background: #d4edda; color: #155724; padding: 1em; margin: 1em 0; border-radius: 5px; text-align: center; font-weight: bold;">
      <?= $mensajeBienvenida ?>
    </div>
  <?php endif; ?>
  <!-- 🪟 MODAL LOGIN -->
  <div class="login-modal" id="loginModal">
    <div class="login-box">
      <span class="close-modal" id="closeLogin">&times;</span>

      <h2>Iniciar sesión</h2>

      <!-- LOGIN -->
      <form id="loginForm">
        <input type="email" name="correo" placeholder="Correo" required>
        <input type="password" name="password" placeholder="Contraseña" required>

        <button type="submit">Entrar</button>
      </form>

      <p id="loginError" class="login-error"></p>

      <!-- OPCIONES -->
      <div class="login-options">
        <a href="registrarse.php" class="btn-crear">
          Crear cuenta
        </a>

        <a href="#" id="openRecover">¿Olvidaste tu contraseña?</a>
      </div>

      <!-- REGISTRO -->
      <form id="registerForm" class="hidden">
        <h3>Crear cuenta</h3>
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="email" name="correo" placeholder="Correo" required>
        <input type="password" name="password" placeholder="Contraseña" required>

        <button type="submit">Registrarme</button>
        <p class="back-login">← Volver a iniciar sesión</p>
      </form>

      <!-- RECUPERAR -->
      <form id="recoverForm" class="hidden">
        <h3>Recuperar contraseña</h3>
        <input type="email" name="correo" placeholder="Correo registrado" required>

        <button type="submit">Enviar enlace</button>
        <p class="back-login">← Volver a iniciar sesión</p>
      </form>

    </div>
  </div>

  <header class="header_cliente">
    <div class="header_left">
      <a href="tienda.php" class="logo">
        <img src="img/iconosinfondotitulo.png" alt="GoShop">
        <span>GoShop</span>
      </a>
    </div>

    <nav class="header_nav">
      <a href="#productos">Productos</a>
      <a href="#ofertas">Ofertas</a>
      <a href="#contacto">Contacto</a>

      <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'cliente'): ?>
        <a href="seccion_cliente.php" class="btn-compras">🛍️ Compras</a>
      <?php endif; ?>
    </nav>


    <div class="header_right">
      <?php if ($puedeComprar): ?>
        <button id="cartBtn" class="cart-btn">
          🛒 <span id="cartCount">0</span>
        </button>
      <?php endif; ?>

      <button class="btn-theme" onclick="cambiarColorTema()">🌞</button>

      <?php if (!$usuarioLogueado): ?>
        <!-- 🔐 NO LOGUEADO -->
        <button class="btn-login" id="openLoginModal">
          Iniciar sesión
        </button>


      <?php else: ?>
        <!-- 👤 LOGUEADO -->
        <div class="user-menu" id="userMenu">
          <img src="img/user_icon.png" alt="Usuario">
          <span><?= htmlspecialchars($_SESSION['nombre']) ?></span>

          <div class="user-dropdown" id="userDropdown">
            <a href="perfil.php">👤 Mi perfil</a>
            <a href="mis_pedidos.php">📦 Mis pedidos</a>

            <?php if ($_SESSION['nivel_acceso'] === 'medio'): ?>
              <a href="vendedor/pedidos.php">🛒 Panel vendedor</a>
            <?php endif; ?>

            <?php if ($_SESSION['nivel_acceso'] === 'admin'): ?>
              <a href="admin/dashboard.php">👑 Panel admin</a>
            <?php endif; ?>

            <hr>
            <a href="logout.php" class="logout">🚪 Cerrar sesión</a>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </header>
  <div id="cartOverlay" class="cart-overlay"></div>

  <aside class="cart-panel" id="cartPanel">
    <header class="cart-header">
      <h2>🛒 Carrito</h2>
      <button id="closeCart">✖</button>
    </header>

    <ul id="cartItems"></ul>

    <h3>Total: $<span id="cartTotal">0</span></h3>

    <button id="checkoutBtn">Finalizar pedido</button>
  </aside>

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
      <div class="categoria_productos-wrapper" style="position:relative;">
        <button type="button" class="categoria-scroll-btn left" onclick="scrollCategorias(-1)">&#8592;</button>
        <div class="categoria_productos" id="categoriaProductos">
          <?php while ($cat = $categorias->fetch_assoc()):
            $catClase = strtolower(str_replace(' ', '-', $cat['nombre']));
          ?>
            <button class="btn" onclick="mostrar('<?= $catClase ?>')">
              <?= $cat['nombre'] ?>
            </button>
          <?php endwhile; ?>
          <button class="btn" onclick="mostrar('todos')">Todos</button>
        </div>
        <button type="button" class="categoria-scroll-btn right" onclick="scrollCategorias(1)">&#8594;</button>
      </div>


      <!-- 🔹 Productos desde base de datos -->
      <div class="productos_cards">
        <?php while ($producto = $resultadoProductos->fetch_assoc()): ?>
          <?php
          $categoria = strtolower(trim($producto['categoria']));
          $claseCategoria = str_replace(' ', '-', $categoria);
          ?>
          <div class="card item <?= $claseCategoria ?>">
            <img src="img/<?= $claseCategoria ?>.jpg" alt="<?= htmlspecialchars($producto['nombre']) ?>">
            <h3><?= htmlspecialchars($producto['nombre']) ?></h3>
            <p><?= ucfirst($producto['categoria']) ?></p>
            <span>$<?= number_format($producto['precio'], 2, ',', '.') ?></span>

            <?php if ($puedeComprar): ?>
              <button class="btn add-to-cart"
                data-id="<?= $producto['id_producto'] ?>"
                data-nombre="<?= htmlspecialchars($producto['nombre']) ?>"
                data-precio="<?= $producto['precio'] ?>">
                Comprar
              </button>
            <?php else: ?>
              <button class="btn btn-login open-login">
                Inicia sesión para comprar
              </button>
            <?php endif; ?>
          </div>
        <?php endwhile; ?>

      </div>
    </section>
  </main>

  <footer class="footer_cliente">
    <div class="footer_container">

      <div class="footer_brand">
        <img src="img/logo.png" alt="Go Shop">
        <h3>Go Shop</h3>
        <p>Tu pastelería favorita, compra fácil, rápida y con amor artesanal.</p>

        <div class="footer_social">
          <a href="#">🌐</a>
          <a href="#">📘</a>
          <a href="#">📸</a>
        </div>
      </div>

      <div class="footer_links">
        <h4>Empresa</h4>
        <a href="#">Sobre nosotros</a>
        <a href="#">Tiendas</a>
        <a href="#">Contacto</a>
      </div>

      <div class="footer_links">
        <h4>Ayuda</h4>
        <a href="#">Soporte</a>
        <a href="#">Políticas</a>
        <a href="#">Términos</a>
      </div>

    </div>

    <div class="footer_bottom">
      © 2026 Go Shop
      <span>Todos los derechos reservados</span>
    </div>
  </footer>





  <script>
    /* ===============================
   🔐 ESTADO SESIÓN
=============================== */
    const usuarioLogueado = <?= isset($_SESSION['id_usuario']) ? 'true' : 'false' ?>;

    /* ===============================
       🎂 FILTRAR PRODUCTOS
    =============================== */
    function mostrar(categoria) {
      document.querySelectorAll('.item').forEach(item => {
        item.style.display =
          categoria === 'todos' || item.classList.contains(categoria) ?
          'block' :
          'none';
      });
    }

    /* ===============================
       🛒 CARRITO
    =============================== */
    const cart = [];

    const cartPanel = document.getElementById('cartPanel');
    const cartOverlay = document.getElementById('cartOverlay');
    const cartBtn = document.getElementById('cartBtn');
    const closeCart = document.getElementById('closeCart');
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    const cartCount = document.getElementById('cartCount');

    /* Abrir carrito */
    cartBtn?.addEventListener('click', () => {
      if (!usuarioLogueado) {
        loginModal.style.display = "flex";
        return;
      }
      cartPanel.classList.add('activo');
      cartOverlay.classList.add('activo');
    });

    /* Cerrar carrito */
    function cerrarCarrito() {
      cartPanel.classList.remove('activo');
      cartOverlay.classList.remove('activo');
    }

    closeCart?.addEventListener('click', cerrarCarrito);
    cartOverlay?.addEventListener('click', cerrarCarrito);

    /* ===============================
       ➕ AGREGAR AL CARRITO
    =============================== */
    document.addEventListener('click', e => {
      if (e.target.classList.contains('add-to-cart')) {

        if (!usuarioLogueado) {
          loginModal.style.display = "flex";
          return;
        }

        const id = e.target.dataset.id;
        const nombre = e.target.dataset.nombre;
        const precio = parseFloat(e.target.dataset.precio);

        const existente = cart.find(p => p.id === id);

        if (existente) {
          existente.cantidad++;
        } else {
          cart.push({
            id,
            nombre,
            precio,
            cantidad: 1
          });
        }

        actualizarCarrito();
      }
    });

    /* Actualizar carrito */
    function actualizarCarrito() {
      cartItems.innerHTML = '';
      let total = 0;
      let cantidadTotal = 0;

      cart.forEach((item, index) => {
        total += item.precio * item.cantidad;
        cantidadTotal += item.cantidad;

        const li = document.createElement('li');
        li.classList.add('cart-item');

        li.innerHTML = `
      <div class="cart-item-info">
        <strong>${item.nombre}</strong>
        <small>$${item.precio.toFixed(2)}</small>
      </div>

      <div class="cart-item-controls">
        <button onclick="cambiarCantidad(${index}, -1)">−</button>
        <span>${item.cantidad}</span>
        <button onclick="cambiarCantidad(${index}, 1)">+</button>
        <button class="remove" onclick="eliminarItem(${index})">🗑</button>
      </div>
    `;

        cartItems.appendChild(li);
      });

      cartTotal.textContent = total.toFixed(2);
      cartCount.textContent = cantidadTotal;
    }

    function cambiarCantidad(index, cambio) {
      cart[index].cantidad += cambio;

      if (cart[index].cantidad <= 0) {
        cart.splice(index, 1);
      }

      actualizarCarrito();
    }

    function eliminarItem(index) {
      cart.splice(index, 1);
      actualizarCarrito();
    }


    /* ===============================
       👤 MENÚ USUARIO
    =============================== */
    const userMenu = document.getElementById('userMenu');
    const userDropdown = document.getElementById('userDropdown');

    userMenu?.addEventListener('click', e => {
      e.stopPropagation();
      userDropdown.classList.toggle('activo');
    });

    document.addEventListener('click', () => {
      userDropdown?.classList.remove('activo');
    });

    /* ===============================
       🔐 MODAL LOGIN
    =============================== */
    const loginModal = document.getElementById("loginModal");
    const closeLogin = document.getElementById("closeLogin");

    const loginForm = document.getElementById("loginForm");
    const registerForm = document.getElementById("registerForm");
    const recoverForm = document.getElementById("recoverForm");

    const loginError = document.getElementById("loginError");
    const openRecover = document.getElementById("openRecover");

    /* Abrir / cerrar modal */
    document.addEventListener("click", e => {

      if (e.target.id === "openLoginModal" || e.target.classList.contains("open-login")) {
        loginModal.style.display = "flex";
        mostrarLogin();
      }

      if (e.target === loginModal || e.target.id === "closeLogin") {
        loginModal.style.display = "none";
      }
    });

    /* ===============================
       🔁 CAMBIO DE FORMULARIOS
    =============================== */
    function mostrarLogin() {
      loginForm.classList.remove("hidden");
      registerForm.classList.add("hidden");
      recoverForm.classList.add("hidden");
    }

    function mostrarRegistro() {
      loginForm.classList.add("hidden");
      registerForm.classList.remove("hidden");
      recoverForm.classList.add("hidden");
    }

    function mostrarRecuperar() {
      loginForm.classList.add("hidden");
      registerForm.classList.add("hidden");
      recoverForm.classList.remove("hidden");
    }

    openRecover?.addEventListener("click", e => {
      e.preventDefault();
      mostrarRecuperar();
    });

    document.querySelectorAll(".back-login").forEach(btn => {
      btn.addEventListener("click", mostrarLogin);
    });

    /* ===============================
       📡 LOGIN AJAX
    =============================== */
    loginForm?.addEventListener("submit", e => {
      e.preventDefault();

      fetch("login_ajax.php", {
          method: "POST",
          body: new FormData(loginForm)
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            location.reload();
          } else {
            loginError.textContent = data.message;
          }
        });
    });

    /* ===============================
       📝 REGISTRO AJAX
    =============================== */
    registerForm?.addEventListener("submit", e => {
      e.preventDefault();

      fetch("registro_ajax.php", {
          method: "POST",
          body: new FormData(registerForm)
        })
        .then(res => res.json())
        .then(data => {
          alert(data.message);
          if (data.success) mostrarLogin();
        });
    });

    /* ===============================
       🔁 RECUPERAR CONTRASEÑA
    =============================== */
    recoverForm?.addEventListener("submit", e => {
      e.preventDefault();

      fetch("recuperar_ajax.php", {
          method: "POST",
          body: new FormData(recoverForm)
        })
        .then(res => res.json())
        .then(data => alert(data.message));
    });

    /* ===============================
       ⏩ SCROLL CATEGORÍAS
    =============================== */
    function scrollCategorias(dir) {
      const cont = document.getElementById('categoriaProductos');
      cont.scrollBy({
        left: dir * 120,
        behavior: 'smooth'
      });
    }
  </script>


</body>

</html>