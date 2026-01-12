<?php
session_start();
require 'conexion/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

/* USUARIO */
$stmt = $conexion->prepare("
    SELECT nombre, Apellido, correo, telefono, codigo_region,
           tipo_usuario, nivel_acceso, fecha_creacion
    FROM usuarios
    WHERE id = ?
");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

/* PEDIDOS */
$stmtP = $conexion->prepare("
    SELECT id_pedido, id_local, fecha_pedido, estado
    FROM pedidos
    WHERE id_usuario = ?
    ORDER BY fecha_pedido DESC
");
$stmtP->bind_param("i", $id_usuario);
$stmtP->execute();
$pedidos = $stmtP->get_result();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mi Perfil</title>

    <style>
        :root {
            --primario: #1f2933;
            --secundario: #374151;
            --fondo: #f4f6f8;
            --blanco: #ffffff;
            --borde: #e5e7eb;
            --verde: #22c55e;
            --amarillo: #facc15;
            --rojo: #ef4444;
        }

        * {
            box-sizing: border-box;
            font-family: Segoe UI, Arial
        }

        body {
            background: var(--fondo);
            margin: 0;
            padding: 40px
        }

        .perfil-container {
            max-width: 1200px;
            margin: auto
        }

        .perfil-header {
            background: var(--blanco);
            padding: 30px;
            border-radius: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
        }

        .perfil-header h1 {
            margin: 0;
            color: var(--primario)
        }

        .perfil-header p {
            margin: 5px 0;
            color: #555
        }

        .perfil-grid {
            margin-top: 30px;
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 30px;
        }

        /* TARJETAS */
        .card {
            background: var(--blanco);
            border-radius: 14px;
            padding: 25px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, .08);
        }

        .card h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: var(--primario);
            font-size: 18px;
            border-bottom: 1px solid var(--borde);
            padding-bottom: 10px;
        }

        .card p {
            margin: 10px 0;
            color: #555
        }

        /* TABLA */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            font-size: 13px;
            color: #777;
            border-bottom: 1px solid var(--borde);
            padding-bottom: 10px;
        }

        td {
            padding: 14px 0;
            border-bottom: 1px solid var(--borde);
            font-size: 14px;
        }

        .estado {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .pendiente {
            background: #fef3c7;
            color: #92400e
        }

        .completado {
            background: #dcfce7;
            color: #166534
        }

        .cancelado {
            background: #fee2e2;
            color: #991b1b
        }

        .acciones a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            font-weight: 600;
            color: var(--secundario);
        }

        .acciones a:hover {
            text-decoration: underline
        }

        @media(max-width:900px) {
            .perfil-grid {
                grid-template-columns: 1fr
            }
        }

        .btn-detalle {
            padding: 6px 14px;
            border-radius: 20px;
            background: #1f2933;
            color: white;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: .2s;
        }

        .btn-detalle:hover {
            background: #374151;
        }
    </style>
</head>

<body>

    <div class="perfil-container">

        <!-- HEADER -->
        <div class="perfil-header">
            <div>
                <h1><?= $usuario['nombre'] . " " . $usuario['Apellido'] ?></h1>
                <p><?= $usuario['correo'] ?></p>
            </div>
            <div>
                <strong><?= ucfirst($usuario['tipo_usuario']) ?></strong><br>
                <small>Registrado: <?= $usuario['fecha_creacion'] ?></small>
            </div>
        </div>

        <!-- GRID -->
        <div class="perfil-grid">

            <!-- INFO -->
            <div>
                <div class="card">
                    <h3>Información Personal</h3>
                    <p><strong>Teléfono:</strong> <?= $usuario['telefono'] ?? 'No registrado' ?></p>
                    <p><strong>Región:</strong> <?= $usuario['codigo_region'] ?? 'No registrado' ?></p>
                </div>

                <div class="card">
                    <h3>Cuenta</h3>
                    <p><strong>Nivel:</strong> <?= $usuario['nivel_acceso'] ?></p>
                    <p><strong>Tipo:</strong> <?= $usuario['tipo_usuario'] ?></p>
                </div>
            </div>

            <!-- HISTORIAL -->
            <div class="card">
                <h3>Historial de Compras</h3>

                <?php if ($pedidos->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Local</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php while ($p = $pedidos->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?= $p['id_pedido'] ?></td>
                                    <td><?= $p['id_local'] ?></td>
                                    <td><?= $p['fecha_pedido'] ?></td>
                                    <td>
                                        <span class="estado <?= $p['estado'] ?>">
                                            <?= ucfirst($p['estado']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a class="btn-detalle"
                                            href="pedido_detalle.php?id=<?= $p['id_pedido'] ?>">
                                            Ver detalle
                                        </a>
                                    </td>

                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No tienes pedidos registrados.</p>
                <?php endif; ?>

                <div class="acciones">
                    <a href="index.php">⬅ Volver a la tienda</a><br>
                    <a href="logout.php">🔒 Cerrar sesión</a>
                </div>
            </div>

        </div>
    </div>

</body>

</html>