<?php
include 'conexion/conexion.php';

$filtroEstado = $_GET['estado'] ?? "";

$consultaPedidos = "
SELECT
    ped.id_pedido,
    usu.nombre AS cliente,
    loc.nombre AS local,
    ped.fecha_pedido,
    ped.estado
FROM pedidos ped
LEFT JOIN usuarios usu ON ped.id_usuario = usu.id
LEFT JOIN locales loc ON ped.id_local = loc.id_local
";

$params = [];
$tipos = "";

if (!empty($filtroEstado)) {
    $consultaPedidos .= " WHERE LOWER(ped.estado) = ?";
    $params[] = strtolower($filtroEstado);
    $tipos .= "s";
}

$consultaPedidos .= " ORDER BY ped.fecha_pedido DESC";

if (!empty($params)) {
    $stmt = $conexion->prepare($consultaPedidos);
    $stmt->bind_param($tipos, ...$params);
    $stmt->execute();
    $resultadoPedidos = $stmt->get_result();
} else {
    $resultadoPedidos = $conexion->query($consultaPedidos);
}


// Consulta resumen de pedidos por estado
$consultaResumen = "
SELECT estado, COUNT(*) AS cantidad 
FROM pedidos 
GROUP BY estado
";
$resultadoResumen = $conexion->query($consultaResumen);

$estados = [];
$cantidades = [];
$totalPedidos = 0;

while ($row = $resultadoResumen->fetch_assoc()) {
    $estados[] = ucfirst($row['estado']);
    $cantidades[] = (int)$row['cantidad'];
    $totalPedidos += $row['cantidad'];
}

// Últimos pedidos

$consultaUltimos = "
SELECT ped.id_pedido, usu.nombre AS cliente, ped.estado, ped.fecha_pedido
FROM pedidos ped
LEFT JOIN usuarios usu ON ped.id_usuario = usu.id
ORDER BY ped.fecha_pedido DESC
LIMIT 5
";
$ultimosPedidos = $conexion->query($consultaUltimos);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos</title>
    <link rel="stylesheet" href="css/index.css">
    <!-- CSS de DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
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

        <section class="container" id="container_index">
            <h1>Gestión de Pedidos</h1>
            <div class="form-group">
                <a href="gestionar_productos.php" class="btn">Gestionar Productos</a>
                <a href="crear_pedido.php" class="btn">Crear Pedido</a>
            </div>
            <h1>Pedidos Existentes</h1>
            <form method="GET" class="form-group">
                <label for="estado">Filtrar por Estado:</label>
                <select class="select" name="estado" id="estado" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <option value="pendiente" <?= $filtroEstado === "pendiente" ? "selected" : "" ?>>Pendiente</option>
                    <option value="completado" <?= $filtroEstado === "completado" ? "selected" : "" ?>>Completado</option>
                    <option value="cancelado" <?= $filtroEstado === "cancelado" ? "selected" : "" ?>>Cancelado</option>
                </select>
            </form>
            <table id="myTable" class="table table-dark">
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
                <tbody>
                    <?php
                    while ($mostrar = $resultadoPedidos->fetch_assoc()) {
                    ?>
                        <tr>
                            <td><?= $mostrar['id_pedido'] ?></td>
                            <td><?= $mostrar['cliente'] ?></td> <!-- Nombre del cliente -->
                            <td><?= $mostrar['local'] ?></td> <!-- Nombre del local -->
                            <td><?= $mostrar['fecha_pedido'] ?></td>
                            <td>
                                <span class="badge <?= $mostrar['estado'] ?>">
                                    <?= ucfirst($mostrar['estado']) ?>
                                </span>
                            </td>

                            <td class="acciones">
                                <a href="editar_pedido.php?id=<?= $mostrar['id_pedido'] ?>" title="Editar">✏️</a>
                                <a href="eliminar_pedido.php?id=<?= $mostrar['id_pedido'] ?>"
                                    title="Eliminar"
                                    onclick="return confirm('¿Eliminar pedido?')">🗑️</a>
                            </td>

                        </tr>

                    <?php
                    }
                    ?>
                </tbody>


            </table>
        </section>
        <section class="container" id="dashboard_pedidos">
            <h1 class="titulo_hog" style="color: var(--color-titulo); margin-bottom: 1rem;">📊 Panel Informativo de Pedidos</h1>

            <!-- Tarjetas de resumen -->
            <div class="dashboard-cards">
                <div class="card">
                    <h2>Total de Pedidos</h2>
                    <span style="color: var(--color-encabezado); font-size: 1.8em;">
                        <?= $totalPedidos ?>
                    </span>
                </div>
                <div class="card">
                    <h2>Pendientes</h2>
                    <span style="color: #ffc107; font-size: 1.8em;">
                        <?= $cantidades[array_search('Pendiente', $estados)] ?? 0 ?>
                    </span>
                </div>
                <div class="card">
                    <h2>Completados</h2>
                    <span style="color: #28a745; font-size: 1.8em;">
                        <?= $cantidades[array_search('Completado', $estados)] ?? 0 ?>
                    </span>
                </div>
                <div class="card">
                    <h2>Cancelados</h2>
                    <span style="color: #dc3545; font-size: 1.8em;">
                        <?= $cantidades[array_search('Cancelado', $estados)] ?? 0 ?>
                    </span>
                </div>
            </div>

            <!-- Gráfico -->
            <div class="card grafico-card">
                <h2 style="color: var(--color-encabezado); margin-bottom: 1rem;">Distribución de Estados</h2>
                <canvas id="chartEstados" style="width:100%; height:300px;"></canvas>
            </div>

            <!-- Últimos pedidos -->
            <div class="card">
                <h2 style="color: var(--color-encabezado); margin-bottom: 1rem;">Últimos Pedidos</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($p = $ultimosPedidos->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $p['id_pedido'] ?></td>
                                <td><?= $p['cliente'] ?></td>
                                <td><?= ucfirst($p['estado']) ?></td>
                                <td><?= $p['fecha_pedido'] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const ctx = document.getElementById('chartEstados');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: <?= json_encode($estados) ?>,
                        datasets: [{
                            data: <?= json_encode($cantidades) ?>,
                            backgroundColor: [
                                '#ffc107',
                                '#28a745',
                                '#dc3545',
                                '#5f8fa0'
                            ]
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            </script>
        </section>
    </main>
    <footer class="footer">
        <p>&copy; 2023 Juli's. Todos los derechos reservados.</p>
        <p>Desarrollado por Julián</p>
    </footer>
    <script src="js/script.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- JS de DataTables -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <!-- Inicialización de DataTable -->
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
                }
            });
        });
    </script>

</body>

</html>