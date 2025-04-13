<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "uwu");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Consultar datos de clientes y locales
$clientes = $conexion->query("SELECT id_cliente, nombre, telefono FROM clientes");
$locales = $conexion->query("SELECT id_local, nombre FROM locales");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Pedido</title>
    <link rel="stylesheet" href="style.css">
    <script>
        // Función para mostrar el número de celular del cliente seleccionado
        function mostrarTelefono() {
            const selectCliente = document.getElementById('cliente');
            const telefono = selectCliente.options[selectCliente.selectedIndex].dataset.telefono;
            document.getElementById('telefono').innerText = telefono ? `Teléfono: ${telefono}` : '';
        }

        function cargarProductos(idLocal) {
            fetch(`obtener_productos.php?local=${idLocal}`)
                .then(response => response.json())
                .then(data => {
                    const contenedor = document.getElementById('productos-container');
                    if (data.success) {
                        let html = '<ul>';
                        data.productos.forEach(producto => {
                            html += `
                                <li>
                                    <label>
                                        <input  type="checkbox" name="productos[${producto.id_producto}][seleccionado]" value="1">
                                        ${producto.nombre} (Stock: ${producto.cantidad_producto})
                                    </label>
                                    <input  type="number" name="productos[${producto.id_producto}][cantidad]" 
                                           placeholder="Cantidad" min="1" max="${producto.cantidad_producto}" disabled>
                                </li>
                            `;
                        });
                        html += '</ul>';
                        contenedor.innerHTML = html;

                        // Habilitar el input de cantidad al seleccionar un producto
                        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                            checkbox.addEventListener('change', function() {
                                const cantidadInput = this.closest('li').querySelector('input[type="number"]');
                                cantidadInput.disabled = !this.checked;
                            });
                        });
                    } else {
                        contenedor.innerHTML = `<p>${data.message}</p>`;
                    }
                })
                .catch(error => {
                    console.error('Error al cargar los productos:', error);
                    document.getElementById('productos-container').innerHTML = `<p>Error al cargar los productos.</p>`;
                });
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Crear Nuevo Pedido</h1>
        <form action="guardar_pedido.php" method="POST">
            <!-- Seleccionar Cliente -->
            <div class="form-group">
                <label for="cliente">Seleccione un Cliente:</label>
                <select name="cliente" id="cliente" required onchange="mostrarTelefono()">
                    <option value="">Seleccione un cliente</option>
                    <?php while ($cliente = $clientes->fetch_assoc()) : ?>
                        <option value="<?= $cliente['id_cliente'] ?>" data-telefono="<?= $cliente['telefono'] ?>">
                            <?= $cliente['nombre'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <p id="telefono"></p> <!-- Mostrar número de teléfono -->
            </div>

            <!-- Seleccionar Local -->
            <div class="form-group">
                <label for="local">Seleccione un Local:</label>
                <select name="local" id="local" required onchange="cargarProductos(this.value)">
                    <option value="">Seleccione una tienda</option>
                    <?php while ($local = $locales->fetch_assoc()) : ?>
                        <option value="<?= $local['id_local'] ?>"><?= $local['nombre'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Mostrar Productos Disponibles -->
            <div class="form-group">
                <label for="productos">Seleccione los Productos y Cantidades:</label>
                <div id="productos-container">
                    <p>Seleccione una tienda para cargar los productos disponibles.</p>
                </div>
            </div>

            <!-- Botón Guardar -->
            <div class="form-group">
                <button type="submit" class="btn">Guardar Pedido</button>
            </div>
                    <!-- Botón de regresar a Gestión de Pedidos -->
        <div class="form-group">
            <a href="index.php" class="btn">Regresar a Gestión de Pedidos</a>
        </div>

        </form>
    </div>
</body>
</html>

