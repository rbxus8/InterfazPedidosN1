<?php
include 'conexion/conexion.php';

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
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header class="header">
        <a href="#">
            <img
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
        <section class="container">
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
                    <input class="select1" type="number" name="stock" id="stock" min="1" required>
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
        </section>
    </main>
    <footer class="footer">
        <p>&copy; 2023 Juli's. Todos los derechos reservados.</p>
        <p>Desarrollado por Julián</p>
    </footer>
    <script src="js/script.js"></script>
</body>

</html>