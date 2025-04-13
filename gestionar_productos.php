<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "uwu");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener productos de Negocio A
$productosA = $conexion->query("SELECT p.id_producto, p.nombre, p.codigo_producto, p.unidad_medida, b.cantidad_producto 
                                FROM productos p
                                INNER JOIN bodega b ON p.id_producto = b.id_producto
                                WHERE b.id_local = 1"); // 1 es el ID de Negocio A

// Obtener productos de Negocio B
$productosB = $conexion->query("SELECT p.id_producto, p.nombre, p.codigo_producto, p.unidad_medida, b.cantidad_producto 
                                FROM productos p
                                INNER JOIN bodega b ON p.id_producto = b.id_producto
                                WHERE b.id_local = 2"); // 2 es el ID de Negocio B
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Gestión de Productos</h1>

        <!-- Botón de Regresar a Gestión de Pedidos -->
        <div class="form-group">
            <a href="index.php" class="btn">Regresar a Gestión de Pedidos</a>
        </div>

        <!-- Tabla de Productos de Negocio A -->
        <h2>Productos - Negocio A</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>ID Producto</th>
                    <th>Código Producto</th>
                    <th>Nombre</th>
                    <th>Unidad</th> <!-- Columna para Unidad -->
                    <th>Stock Disponible</th>
                    <th>Agregar Stock</th>
                    <th>Eliminar Producto</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($producto = $productosA->fetch_assoc()) : ?>
                    <tr>
                        <td><?= $producto['id_producto'] ?></td>
                        <td><?= $producto['codigo_producto'] ?></td>
                        <td><?= $producto['nombre'] ?></td>
                        <td><?= $producto['unidad_medida'] ?></td> <!-- Mostrar la unidad -->
                        <td><?= $producto['cantidad_producto'] ?></td>
                        <td>
                            <form  action="agregar_stock.php" method="POST">
                                <input  type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
                                <input  type="number" name="cantidad" min="1" placeholder="Cantidad a agregar" required>
                                <button type="submit" class="btn">Agregar Stock</button>
                            </form>
                        </td>
                        <td>
                            <form action="eliminar_producto.php" method="POST">
                                <input  type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
                                <button type="submit" class="btn">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Tabla de Productos de Negocio B -->
        <h2>Productos - Negocio B</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>ID Producto</th>
                    <th>Código Producto</th>
                    <th>Nombre</th>
                    <th>Unidad</th> <!-- Columna para Unidad -->
                    <th>Stock Disponible</th>
                    <th>Agregar Stock</th>
                    <th>Eliminar Producto</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($producto = $productosB->fetch_assoc()) : ?>
                    <tr>
                        <td><?= $producto['id_producto'] ?></td>
                        <td><?= $producto['codigo_producto'] ?></td>
                        <td><?= $producto['nombre'] ?></td>
                        <td><?= $producto['unidad_medida'] ?></td> <!-- Mostrar la unidad -->
                        <td><?= $producto['cantidad_producto'] ?></td>
                        <td>
                            <form action="agregar_stock.php" method="POST">
                                <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
                                <input type="number" name="cantidad" min="1" placeholder="Cantidad a agregar" required>
                                <button type="submit" class="btn">Agregar Stock</button>
                            </form>
                        </td>
                        <td>
                            <form action="eliminar_producto.php" method="POST">
                                <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
                                <button type="submit" class="btn">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Formulario para Agregar Nuevos Productos -->
        <h3>Agregar Nuevos Productos</h3>
        <form action="agregar_producto.php" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre del Producto:</label>
                <input class="select1" type="text" name="nombre" id="nombre" required>
            </div>
            <div class="form-group">
                <label for="stock">Cantidad de Stock:</label>
                <input class="select1"type="number" name="stock" id="stock" min="1" required>
            </div>
            <div class="form-group">
                <label for="local">Seleccionar Local:</label>
                <select class="select1" name="local" id="local" required>
                    <option value="1">Negocio A</option>
                    <option value="2">Negocio B</option>
                </select>
            </div>
            <button type="submit" class="btn">Agregar Producto</button>
        </form>
    </div>
</body>
</html>


