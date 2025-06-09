<?php
include 'conexion.php';

// Obtener el ID del pedido
$idPedido = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener datos del pedido
$consultaPedido = "
    SELECT pedidos.*, clientes.nombre AS cliente, locales.nombre AS local 
    FROM pedidos
    JOIN clientes ON pedidos.id_cliente = clientes.id_cliente
    JOIN locales ON pedidos.id_local = locales.id_local
    WHERE pedidos.id_pedido = ?";
$stmtPedido = $conexion->prepare($consultaPedido);
$stmtPedido->bind_param("i", $idPedido);
$stmtPedido->execute();
$pedido = $stmtPedido->get_result()->fetch_assoc();

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
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <div><a href="#">Juli's</a></ <a href="#"><img
                src="img/Carrito_de_Compras.png"
                alt="nombre_icon_goshop"
                style="height: 1.5em ; ">
            </a>
            <button onclick="cambiarColorTema()" class="chance_color" id="chance_color">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-brightness-high" viewBox="0 0 16 16">
                    <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6m0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8M8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0m0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13m8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5M3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8m10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0m-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0m9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707M4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708" />
                </svg>
            </button>>
    </header>
    <main>
        <div class="container">
            <h1>Editar Pedido</h1>
            <form action="actualizar_pedido.php" method="POST">
                <input type="hidden" name="id_pedido" value="<?= $idPedido ?>">

                <!-- Información del pedido -->
                <div class="form-group">
                    <p><strong>Cliente:</strong> <?= $pedido['cliente'] ?></p>
                    <p><strong>Local:</strong> <?= $pedido['local'] ?></p>
                    <p><strong>Fecha:</strong> <?= $pedido['fecha_pedido'] ?></p>
                </div>

                <!-- Cambiar estado del pedido -->
                <div class="form-group">
                    <label for="estado">Estado del Pedido:</label>
                    <select name="estado" id="estado" required>
                        <option value="pendiente" <?= $pedido['estado'] === "pendiente" ? "selected" : "" ?>>Pendiente</option>
                        <option value="completado" <?= $pedido['estado'] === "completado" ? "selected" : "" ?>>Completado</option>
                        <option value="cancelado" <?= $pedido['estado'] === "cancelado" ? "selected" : "" ?>>Cancelado</option>
                    </select>
                </div>

                <!-- Productos actuales del pedido -->
                <div class="form-group">
                    <h3>Productos del Pedido</h3>
                    <ul>
                        <?php while ($producto = $productos->fetch_assoc()) : ?>
                            <li>
                                <label>
                                    <?= $producto['nombre'] ?>
                                    (Stock disponible: <?= $producto['stock_disponible'] ?>)
                                    - Cantidad actual:
                                    <input type="number" name="productos[<?= $producto['id_producto'] ?>][cantidad]"
                                        value="<?= $producto['cantidad_seleccionada'] ?>"
                                        min="1" max="<?= $producto['stock_disponible'] ?>" required>
                                    <div>
                                        <input type="checkbox" name="productos[<?= $producto['id_producto'] ?>][eliminar]" value="1">
                                        <span>Eliminar Producto</span>
                                    </div>
                                </label>

                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>

                <!-- Agregar nuevos productos -->
                <div class="form-group">
                    <h3>Agregar Nuevos Productos</h3>
                    <ul>
                        <?php while ($productoDisponible = $productosDisponibles->fetch_assoc()) : ?>
                            <li>
                                <label>
                                    <div>
                                        <input type="checkbox" name="nuevos_productos[<?= $productoDisponible['id_producto'] ?>][seleccionado]"
                                            value="1"
                                            onchange="habilitarCantidad(<?= $productoDisponible['id_producto'] ?>)">
                                        <?= $productoDisponible['nombre'] ?>
                                        (Stock disponible: <?= $productoDisponible['cantidad_producto'] ?>)
                                    </div>
                                    <input type="number" name="nuevos_productos[<?= $productoDisponible['id_producto'] ?>][cantidad]"
                                        placeholder="Cantidad"
                                        min="1"
                                        max="<?= $productoDisponible['cantidad_producto'] ?>"
                                        disabled id="cantidad_<?= $productoDisponible['id_producto'] ?>">
                                </label>


                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>



                <div>
                    <button type="submit" class="btn">Actualizar Pedido</button><!-- Botón Guardar -->
                    <a href="index.php" class="btn">Regresar a Pedidos Existentes</a> <!-- Botón Regresar a Pedidos Existentes -->
                </div>



            </form>
        </div>
    </main>
    <footer>
        <p>&copy; 2023 Juli's. Todos los derechos reservados.</p>
        <p>Desarrollado por Julián</p>
    </footer>
    <script src="script.js"></script>
</body>

</html>