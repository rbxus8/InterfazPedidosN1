<?php
include 'conexion/conexion.php';
//
// Obtener el ID del pedido
$idPedido = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener datos del pedido
$consultaPedido = "
    SELECT pedidos.*, 
           usuarios.nombre AS usuario, 
           locales.nombre AS local 
    FROM pedidos
    LEFT JOIN usuarios ON pedidos.id_cliente = usuarios.id
    LEFT JOIN locales ON pedidos.id_local = locales.id_local
    WHERE pedidos.id_pedido = ?";


$stmtPedido = $conexion->prepare($consultaPedido);
$stmtPedido->bind_param("i", $idPedido);
$stmtPedido->execute();
$pedido = $stmtPedido->get_result()->fetch_assoc();

if (!$pedido) {
    echo "<p>No se encontró el pedido solicitado.</p>";
    exit;
}

// Obtener los productos del pedido
$consultaProductos = "
    SELECT productos.id_producto, productos.nombre, historial_productos.accion AS cantidad_seleccionada, 
           bodega.cantidad_producto AS stock_disponible 
    FROM historial_productos
    JOIN productos ON historial_productos.id_producto = productos.id_producto
    JOIN bodega ON productos.id_producto = bodega.id_producto
    WHERE historial_productos.id_pedido = ?";
$stmtProductos = $conexion->prepare($consultaProductos);
$stmtProductos->bind_param("i", $idPedido);
$stmtProductos->execute();
$productos = $stmtProductos->get_result();

// Obtener todos los productos disponibles para el local del pedido
$productosDisponibles = $conexion->query("
    SELECT productos.id_producto, productos.nombre, bodega.cantidad_producto 
    FROM productos
    JOIN bodega ON productos.id_producto = bodega.id_producto
    WHERE bodega.id_local = {$pedido['id_local']}");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pedido</title>
    <link rel="stylesheet" href="css/style.css">
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
    <main>
        <div class="editar-pedido-container">
            <h1 class="titulo-pedido">✏️ Editar Pedido</h1>

            <form action="actualizar_pedido.php" method="POST" class="editar-pedido-form">
                <input type="hidden" name="id_pedido" value="<?= $idPedido ?>">

                <!-- Información del pedido -->
                <section class="card-seccion">
                    <h2 class="seccion-titulo">📋 Información del Pedido</h2>
                    <div class="info-grid">
                        <p><strong>Usuario:</strong> <?= $pedido['usuario'] ?></p>
                        <p><strong>Local:</strong> <?= $pedido['local'] ?></p>
                        <p><strong>Fecha:</strong> <?= $pedido['fecha_pedido'] ?></p>
                    </div>
                </section>

                <!-- Cambiar estado del pedido -->
                <section class="card-seccion">
                    <h2 class="seccion-titulo">⚙️ Estado del Pedido</h2>
                    <label for="estado">Seleccionar estado:</label>
                    <select name="estado" id="estado" class="select-estado"
                        required <?= $pedido['estado'] === "cancelado" ? "disabled" : "" ?>>
                        <option value="pendiente" <?= $pedido['estado'] === "pendiente" ? "selected" : "" ?>>Pendiente</option>
                        <option value="completado" <?= $pedido['estado'] === "completado" ? "selected" : "" ?>>Completado</option>
                        <option value="cancelado" <?= $pedido['estado'] === "cancelado" ? "selected" : "" ?>>Cancelado</option>
                    </select>

                    <?php if ($pedido['estado'] === "cancelado"): ?>
                        <input type="hidden" name="estado" value="cancelado">
                        <p class="mensaje-cancelado">🚫 Este pedido está cancelado y no puede modificarse.</p>
                    <?php endif; ?>
                </section>

                <!-- Productos actuales -->
                <section class="card-seccion">
                    <h2 class="seccion-titulo">🛒 Productos del Pedido</h2>
                    <ul class="lista-productos">
                        <?php while ($producto = $productos->fetch_assoc()) : ?>
                            <li class="producto-item">
                                <div class="producto-info">
                                    <span class="producto-nombre"><?= $producto['nombre'] ?></span>
                                    <small>(Stock: <?= $producto['stock_disponible'] ?>)</small>
                                </div>
                                <?php $maxCantidad = $producto['cantidad_seleccionada'] + $producto['stock_disponible']; ?>
                                <div class="producto-controles">
                                    <input type="number"
                                        name="productos[<?= $producto['id_producto'] ?>][cantidad]"
                                        value="<?= $producto['cantidad_seleccionada'] ?>"
                                        min="1"
                                        max="<?= $maxCantidad ?>"
                                        class="input-cantidad"
                                        required>
                                    <button type="submit"
                                        name="eliminar_producto"
                                        value="<?= $producto['id_producto'] ?>"
                                        class="btn-eliminar">
                                        🗑 Eliminar
                                    </button>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </section>

                <!-- Agregar nuevos productos -->
                <section class="card-seccion">
                    <h2 class="seccion-titulo">➕ Agregar Nuevos Productos</h2>
                    <ul class="lista-nuevos">
                        <?php while ($productoDisponible = $productosDisponibles->fetch_assoc()) : ?>
                            <li class="producto-nuevo">
                                <label>
                                    <input type="checkbox"
                                        name="nuevos_productos[<?= $productoDisponible['id_producto'] ?>][seleccionado]"
                                        value="1"
                                        onchange="habilitarCantidad(<?= $productoDisponible['id_producto'] ?>)"
                                        <?= $pedido['estado'] === "cancelado" ? "disabled" : "" ?>>
                                    <span><?= $productoDisponible['nombre'] ?> (Stock: <?= $productoDisponible['cantidad_producto'] ?>)</span>
                                </label>
                                <input type="number"
                                    name="nuevos_productos[<?= $productoDisponible['id_producto'] ?>][cantidad]"
                                    placeholder="Cantidad"
                                    min="1"
                                    max="<?= $productoDisponible['cantidad_producto'] ?>"
                                    disabled
                                    id="cantidad_<?= $productoDisponible['id_producto'] ?>"
                                    class="input-cantidad"
                                    <?= $pedido['estado'] === "cancelado" ? "disabled" : "" ?>>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </section>

                <!-- Botones de acción -->
                <div class="acciones">
                    <button type="submit" class="btn-guardar">💾 Guardar Cambios</button>
                    <a href="index.php" class="btn-volver">⬅ Regresar</a>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2023 Juli's. Todos los derechos reservados.</p>
        <p>Desarrollado por Julián</p>
    </footer>
    <script src="js/script.js"></script>
</body>

</html>