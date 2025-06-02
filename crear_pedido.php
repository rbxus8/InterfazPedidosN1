<?php
include 'conexion.php';

// Consultar datos de clientes y locales
$usuarios = $conexion->query("SELECT id, nombre, telefono FROM usuarios");
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
    <div class="espacio"></div>
    <section class="container">
        <h1>Crear Nuevo Pedido</h1>
        <form action="guardar_pedido.php" method="POST">
            <!-- Seleccionar Cliente -->
            <div class="form-group">
                <label for="cliente">Seleccione un Cliente:</label>
                <select name="cliente" id="cliente" required onchange="mostrarTelefono()">
                    <option value="">Seleccione un cliente</option>
                    <?php while ($cliente = $usuarios->fetch_assoc()) : ?>
                        <option value="<?= $cliente['id_usuario'] ?>" data-telefono="<?= $cliente['telefono'] ?>">
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
    </section>
    <div class="espacio"></div>
    <footer class="footer">
        <p>&copy; 2023 Juli's. Todos los derechos reservados.</p>
        <p>Desarrollado por Julián</p>
    </footer>
    <script src="script.js"></script>
</body>

</html>