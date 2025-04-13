<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "uwu");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Consultar datos adicionales
$clientes = $conexion->query("SELECT id_cliente, nombre FROM clientes");
$locales = $conexion->query("SELECT id_local, nombre FROM locales");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Nuevo Cliente</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Agregar Nuevo Cliente</h1>
        <form action="guardar_cliente.php" method="POST">
            <!-- Nombre del Cliente -->
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input class="select1" type="text" name="nombre" id="nombre" required>
            </div>

            <!-- Teléfono -->
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input class="select1" type="text" name="telefono" id="telefono" required>
            </div>

            <!-- Correo -->
            <div class="form-group">
                <label for="correo">Correo Electrónico:</label>
                <input class="select1" type="email" name="correo" id="correo">
            </div>

            <!-- Dirección -->
            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <input class="select1" type="text" name="direccion" id="direccion">
            </div>

            <!-- Botón Guardar -->
            <div class="form-group">
                <button type="submit" class="btn">Guardar Cliente</button>
            </div>
            <!-- Botón de regresar a Gestión de Pedidos -->
            <div class="form-group">
                <a href="index.php" class="btn">Regresar a Gestión de Pedidos</a>
            </div>

        </form>
    </div>
</body>

</html>