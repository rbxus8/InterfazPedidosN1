<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "uwu");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

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
    <link rel="stylesheet" href="styles.css">
</head>
<body>
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
                            </label>
                            <input type="checkbox" name="productos[<?= $producto['id_producto'] ?>][eliminar]" value="1">
                            <span>Eliminar Producto</span>
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
                    <input type="checkbox" name="nuevos_productos[<?= $productoDisponible['id_producto'] ?>][seleccionado]" 
                           value="1" 
                           onchange="habilitarCantidad(<?= $productoDisponible['id_producto'] ?>)">
                    <?= $productoDisponible['nombre'] ?> 
                    (Stock disponible: <?= $productoDisponible['cantidad_producto'] ?>)
                </label>
                <input type="number" name="nuevos_productos[<?= $productoDisponible['id_producto'] ?>][cantidad]" 
                       placeholder="Cantidad" 
                       min="1" 
                       max="<?= $productoDisponible['cantidad_producto'] ?>" 
                       disabled id="cantidad_<?= $productoDisponible['id_producto'] ?>">
                
            </li>
        <?php endwhile; ?>
    </ul>
</div>


            <!-- Botón Guardar -->
            <div class="form-group">
                <button type="submit" class="btn">Actualizar Pedido</button>
            </div>

            <!-- Botón Regresar a Pedidos Existentes -->
            <div class="form-group">
                <a href="index.php" class="btn">Regresar a Pedidos Existentes</a>
            </div>
        </form>
    </div>
</body>
<script>
    function habilitarCantidad(idProducto) {
        // Obtiene el input de cantidad para el producto seleccionado
        var cantidadInput = document.getElementById('cantidad_' + idProducto);
        
        // Si la casilla está marcada, habilita el campo de cantidad
        if (document.querySelector('input[name="nuevos_productos[' + idProducto + '][seleccionado]"]:checked')) {
            cantidadInput.disabled = false;
        } else {
            cantidadInput.disabled = true;
        }
    }

     // Función que habilita la cantidad y el botón de agregar
     function habilitarCantidad(idProducto) {
        var cantidadInput = document.getElementById('cantidad_' + idProducto);
        var botonAgregar = document.getElementById('agregar_' + idProducto);

        // Si la casilla está marcada, habilita el campo de cantidad
        if (document.querySelector('input[name="nuevos_productos[' + idProducto + '][seleccionado]"]:checked')) {
            cantidadInput.disabled = false;
        } else {
            cantidadInput.disabled = true;
            botonAgregar.disabled = true;  // Deshabilita el botón si el producto no está seleccionado
        }

        // Si hay una cantidad válida, habilita el botón "Agregar"
        if (cantidadInput.value > 0 && cantidadInput.value <= parseInt(cantidadInput.max)) {
            botonAgregar.disabled = false;  // Habilita el botón "Agregar"
        } else {
            botonAgregar.disabled = true;  // Deshabilita el botón si la cantidad es inválida
        }

        // Escucha el cambio en el input de cantidad
        cantidadInput.addEventListener('input', function() {
            if (cantidadInput.value > 0 && cantidadInput.value <= parseInt(cantidadInput.max)) {
                botonAgregar.disabled = false;
            } else {
                botonAgregar.disabled = true;
            }
        });
    }

    // Función para agregar el producto (sin redirigir)
    function agregarProducto(idProducto) {
        var cantidadInput = document.getElementById('cantidad_' + idProducto);
        var cantidad = cantidadInput.value;

        if (cantidad > 0) {
            // Aquí debes hacer la lógica para agregar el producto a la base de datos sin redirigir
            var formData = new FormData();
            formData.append('id_producto', idProducto);
            formData.append('cantidad', cantidad);

            // Enviar los datos al servidor
            fetch('agregar_producto_pedido.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert('Producto agregado correctamente!');
                // Actualizar la lista de productos o realizar alguna acción después de agregar el producto
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un problema al agregar el producto');
            });
        }
    }
</script>

</html>