<?php
$host = "b1xbvdktlo20sdr39wbi-mysql.services.clever-cloud.com";
$usuario = "udqgmhwed2gjzprz";
$contrasena = "5pDRSAyLkyoXQW28HNBK";
$baseDatos = "b1xbvdktlo20sdr39wbi";

// Crear conexión
$conexion = new mysqli($host, $usuario, $contrasena, $baseDatos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

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
    <header>
        <div><a href="#" style="color: white; text-decoration: none;">Juli's</a></div>
    </header>
    <div class="espacio"></div>
    <section class="container">
        <h1>Gestión de Pedidos</h1>

        <div class="form-group">
            <a href="crear_pedido.php" class="btn">Agregar Nuevo Pedido</a>
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
                    <th>Codigos de Productos</th>
                    <th>Local</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultadoPedidos->num_rows > 0) : ?>
                    <?php while ($pedido = $resultadoPedidos->fetch_assoc()) : ?>
                        <tr>
                            <td><?= htmlspecialchars($pedido['id_pedido']) ?></td>
                            <td><?= htmlspecialchars($pedido['codigos_productos']) ?></td>
                            <td><?= htmlspecialchars($pedido['local']) ?></td>
                            <td><?= htmlspecialchars($pedido['fecha_pedido']) ?></td>
                            <td><?= htmlspecialchars($pedido['estado']) ?></td>
                            <td>
                                <a href="editar_pedido.php?id=<?= $pedido['id_pedido'] ?>" style="margin-right: 10px;">Editar</a>
                                <a href="eliminar_pedido.php?id=<?= $pedido['id_pedido'] ?>"
                                    class="btn-delete" onclick="return confirm4('¿Está seguro de eliminar este pedido?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7">No hay pedidos disponibles.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
    <div class="espacio"></div>
    <footer>
        <p>&copy; 2023 Juli's. Todos los derechos reservados.</p>
        <p>Desarrollado por Julián</p>
    </footer>
</body>

</html>