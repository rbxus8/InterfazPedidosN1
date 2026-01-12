<?php
session_start();
require 'conexion/conexion.php';

if (!isset($_GET['id'])) {
    die("Pedido no especificado");
}

$idPedido = intval($_GET['id']);

$stmt = $conexion->prepare("
    SELECT 
        dp.cantidad,
        dp.precio,
        p.nombre
    FROM detalle_pedido dp
    INNER JOIN productos p 
        ON p.id_producto = dp.id_producto
    WHERE dp.id_pedido = ?
");

$stmt->bind_param("i", $idPedido);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Detalle del Pedido</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            padding: 30px;
        }

        .box {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            max-width: 650px;
            margin: auto;
        }

        h2 {
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #222;
            color: #fff;
        }

        .total {
            font-weight: bold;
            text-align: right;
        }

        .back {
            margin-top: 15px;
            display: inline-block;
            text-decoration: none;
            color: #333;
        }
    </style>
</head>

<body>

    <div class="box">
        <h2>🧾 Detalle del Pedido #<?= $idPedido ?></h2>

        <table>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Subtotal</th>
            </tr>

            <?php
            $total = 0;
            while ($row = $result->fetch_assoc()):
                $subtotal = $row['cantidad'] * $row['precio'];
                $total += $subtotal;
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= $row['cantidad'] ?></td>
                    <td>$<?= number_format($row['precio'], 2) ?></td>
                    <td>$<?= number_format($subtotal, 2) ?></td>
                </tr>
            <?php endwhile; ?>

            <tr>
                <td colspan="3" class="total">Total</td>
                <td class="total">$<?= number_format($total, 2) ?></td>
            </tr>
        </table>

        <a href="perfil.php" class="back">⬅ Volver al perfil</a>
    </div>

</body>

</html>