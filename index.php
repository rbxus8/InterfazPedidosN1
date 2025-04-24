<?php
$host = "b1xbvdktlo20sdr39wbi-mysql.services.clever-cloud.com";
$usuario = "udqgmhwed2gjzprz";         // Cambia esto por el usuario real
$contrasena = "5pDRSAyLkyoXQW28HNBK";   // Nunca publiques esto en Git o público
$baseDatos = "b1xbvdktlo20sdr39wbi"; // Ej: b1xbvdktlo20sdr39wbi

// Crear conexión
$conexion = new mysqli($host, $usuario, $contrasena, $baseDatos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener filtro de estado (si se envía)
$filtroEstado = isset($_GET['estado']) ? $_GET['estado'] : "";

// Consulta de pedidos con datos de productos seleccionados
$consultaPedidos = "
    SELECT 
        pedidos.id_pedido, 
        clientes.nombre AS cliente, 
        locales.nombre AS local, 
        pedidos.fecha_pedido, 
        pedidos.estado, 
        GROUP_CONCAT(DISTINCT productos.codigo_producto SEPARATOR ', ') AS codigos_productos
    FROM pedidos
    JOIN clientes ON pedidos.id_cliente = clientes.id_cliente
    JOIN locales ON pedidos.id_local = locales.id_local
    JOIN historial_productos ON historial_productos.id_pedido = pedidos.id_pedido
    JOIN productos ON historial_productos.id_producto = productos.id_producto";

// Agregar filtro de estado si se envía
if (!empty($filtroEstado)) {
    $consultaPedidos .= " WHERE pedidos.estado = ?";
}

$consultaPedidos .= " GROUP BY pedidos.id_pedido ORDER BY pedidos.fecha_pedido DESC";

$stmt = $conexion->prepare($consultaPedidos);

if (!empty($filtroEstado)) {
    $stmt->bind_param("s", $filtroEstado);
}

$stmt->execute();
$resultadoPedidos = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <div><a href="#">Juli's</a></div>
    </header>
    <div class="espacio"></div>
    <div class="container">
        <h1>Gestión de Pedidos</h1>

        <!-- Opciones para agregar nuevos pedidos y clientes -->
        <div class="form-group">
            <a href="crear_pedido.php" class="btn">Agregar Nuevo Pedido</a>
            <a href="crear_cliente.php" class="btn">Agregar Nuevo Cliente</a>
            <a href="gestionar_productos.php" class="btn">Gestionar Productos</a>
        </div>

        <!-- Filtro de estado -->
        <form method="GET" class="form-group">
            <label for="estado">Filtrar por Estado:</label>
            <select class="select" name="estado" id="estado" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="pendiente" <?= $filtroEstado === "pendiente" ? "selected" : "" ?>>Pendiente</option>
                <option value="completado" <?= $filtroEstado === "completado" ? "selected" : "" ?>>Completado</option>
                <option value="cancelado" <?= $filtroEstado === "cancelado" ? "selected" : "" ?>>Cancelado</option>
            </select>
        </form>

        <!-- Tabla de pedidos existentes -->
        <h2>Pedidos Existentes</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Local</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Códigos de Productos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultadoPedidos->num_rows > 0) : ?>
                    <?php while ($pedido = $resultadoPedidos->fetch_assoc()) : ?>
                        <tr>
                            <td><?= $pedido['id_pedido'] ?></td>
                            <td><?= $pedido['cliente'] ?></td>
                            <td><?= $pedido['local'] ?></td>
                            <td><?= $pedido['fecha_pedido'] ?></td>
                            <td><?= $pedido['estado'] ?></td>
                            <td><?= $pedido['codigos_productos'] ?></td>
                            <td>
                                <a href="editar_pedido.php?id=<?= $pedido['id_pedido'] ?>">Editar</a>
                                <a href="eliminar_pedido.php?id=<?= $pedido['id_pedido'] ?>"
                                    class="btn-delete" onclick="return confirm('¿Está seguro de eliminar este pedido?');">Eliminar</a>
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
    </div>
    <div class="espacio"></div>
    <footer>
        <p>&copy; 2023 Juli's. Todos los derechos reservados.</p>
        <p>Desarrollado por Julián</p>
    </footer>
</body>

</html>