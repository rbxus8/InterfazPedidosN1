<?php
include 'conexion.php'; // Asegúrate de que la conexión a la base de datos esté establecida

// Obtener filtro de estado
$filtroEstado = isset($_GET['estado']) ? $_GET['estado'] : ""; //operador ternario.

// CONSULTA CORREGIDA - VERIFICAR NOMBRES EXACTOS DE COLUMNAS
$consultaPedidos = "
    SELECT
    ped.id_pedido,
    usu.nombre AS cliente,
    loc.nombre AS local,
    ped.fecha_pedido,
    ped.estado,
    GROUP_CONCAT(DISTINCT prod.codigo_producto SEPARATOR ', ') AS codigos_productos
    FROM pedidos ped
    JOIN usuarios usu ON ped.id_cliente = usu.id /* Cambiado de id_usuario a id_cliente */
    JOIN locales loc ON ped.id_local = loc.id_local
    JOIN historial_productos hp ON hp.id_pedido = ped.id_pedido
    JOIN productos prod ON hp.id_producto = prod.id_producto";

// Agregar filtro de estado si existe
if (!empty($filtroEstado)) {
    $consultaPedidos .= " WHERE ped.estado = ?";
}

$consultaPedidos .= " GROUP BY ped.id_pedido ORDER BY ped.fecha_pedido DESC";

// Preparar la consulta con manejo de errores
$stmt = $conexion->prepare($consultaPedidos);
if (!$stmt) {
    die("Error al preparar la consulta: " . $conexion->error);
}

if (!empty($filtroEstado)) {
    $stmt->bind_param("s", $filtroEstado);
}

if (!$stmt->execute()) {
    die("Error al ejecutar la consulta: " . $stmt->error);
}

$resultadoPedidos = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos</title>
    <link rel="stylesheet" href="style.css">

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
        <h1>Gestión de Pedidos</h1>
        <div class="form-group">
            <a href="gestionar_productos.php" class="btn">Gestionar Productos</a>
        </div>
        <form method="GET" class="form-group">
            <label for="estado">Filtrar por Estado:</label>
            <select class="select" name="estado" id="estado" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="pendiente" <?= $filtroEstado === "pendiente" ? "selected" : "" ?>>Pendiente</option>
                <option value="completado" <?= $filtroEstado === "completado" ? "selected" : "" ?>>Completado</option>
                <option value="cancelado" <?= $filtroEstado === "cancelado" ? "selected" : "" ?>>Cancelado</option>
            </select>
        </form>
        <h2>Pedidos Existentes</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>ID cliente</th>
                    <th>Local</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <?php
            $sql = "SELECT * from pedidos limit 10"; // Limitamos a 10 pedidos para evitar sobrecarga
            $result = mysqli_query($conexion, $sql);
            while ($mostrar = mysqli_fetch_array($result)) {
            ?>
                <tbody>
                    <tr>
                        <td><?php echo $mostrar['id_pedido'] ?></td>
                        <td><?php echo $mostrar['id_cliente'] ?></td>
                        <td><?php echo $mostrar['id_local'] ?></td>
                        <td><?php echo $mostrar['fecha_pedido'] ?></td>
                        <td><?php echo $mostrar['estado'] ?></td>
                        <td>
                            <a href="editar_pedido.php?id=<?= $pedidos['id_pedido'] ?>" style="margin-right: 10px;">Editar</a>
                            <a href="eliminar_pedido.php?id=<?= $pedidos['id_pedido'] ?>"
                                class="btn-delete" onclick="return confirm4('¿Está seguro de eliminar este pedido?');">Eliminar</a>
                        </td>
                    </tr>
                <?php
            }
                ?>
                </tbody>
        </table>
    </section>
    <div class="espacio"></div>
    <footer class="footer">
        <p>&copy; 2023 Juli's. Todos los derechos reservados.</p>
        <p>Desarrollado por Julián</p>
    </footer>
    <script src="script.js"></script>
</body>

</html>