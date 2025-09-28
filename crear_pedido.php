<?php
include 'conexion/conexion.php';

// Variables iniciales
$id = $nombre = $correo = $contrasena = $tipo_usuario = $nivel_acceso = $fecha_creacion = $telefono = $codigo_region = $apellido = "";
$mostrarModal = false;

if (isset($_POST['buscar'])) {
    $id = intval($_POST['id']);
    $sql = "SELECT * FROM usuarios WHERE id = $id";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        $nombre = $usuario['nombre'];
        $correo = $usuario['correo'];
        $contrasena = $usuario['contraseña'];
        $tipo_usuario = $usuario['tipo_usuario'];
        $nivel_acceso = $usuario['nivel_acceso'];
        $fecha_creacion = $usuario['fecha_creacion'];
        $telefono = $usuario['telefono'];
        $codigo_region = $usuario['codigo_region'];
        $apellido = $usuario['apellido'];
    } else {
        $mostrarModal = true;
        $codigo_region = "+57";
        $tipo_usuario = "Básico";
        $fecha_creacion = date("Y-m-d H:i:s");
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="css/style.css"> <!-- tu CSS externo -->
    <style>
        /* Modal encima del estilo global */
        .modal {
            display: <?= $mostrarModal ? 'block' : 'none' ?>;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 2000;
        }

        .modal-content {
            background: var(--color-container);
            margin: 5% auto;
            padding: 20px;
            height: 800px;
            overflow-x: hidden;
            width: 1400px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
    </style>
</head>

<body>
    <header class="header">
        <a href="#">
            <img src="img/iconosinfondotitulo.png" alt="nombre_icon_goshop" style="height: 1.5em;">
        </a>
        <button onclick="cambiarColorTema()" class="chance_color" id="chance_color">
            🌞
        </button>
    </header>

    <main>
        <section class="container">
            <a href="index.php" class="btn">Regresar a Pedidos Existentes</a> <!-- Botón Regresar a Pedidos Existentes -->
        </section>
        <section class="container">
            <h1 class="titulo_hog">Gestión de Usuarios</h1>

            <!-- Buscar Usuario -->
            <form method="POST" class="container">
                <div class="form-group">
                    <label for="id">ID Usuario:</label>
                    <input type="number" class="input" name="id" id="id" value="<?= $id ?>">
                    <button type="submit" name="buscar" class="btn">Buscar</button>
                </div>
            </form>

            <!-- Formulario de Edición -->
            <form method="POST" action="guardar_usuario.php" class="form-container">
                <input type="hidden" name="accion" value="actualizar">
                <input type="hidden" name="id" value="<?= $id ?>">

                <div class="form-row">
                    <div class="form-group1"><label>Nombre:</label><input class="input" type="text" name="nombre" value="<?= $nombre ?>"></div>
                    <div class="form-group1"><label>Apellido:</label><input class="input" type="text" name="apellido" value="<?= $apellido ?>"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Correo:</label><input class="input" type="email" name="correo" value="<?= $correo ?>"></div>
                    <div class="form-group"><label>Contraseña:</label><input class="input" type="password" name="contraseña" value="<?= $contrasena ?>"></div>
                </div>
                <input type="hidden" name="tipo_usuario" value="cliente">


                <input type="hidden" name="nivel_acceso" value="basico">
                <div class="form-group"><label>Fecha Creación:</label><input class="input" type="text" name="fecha_creacion" value="<?= $fecha_creacion ?: date('Y-m-d H:i:s') ?>" readonly></div>
                <div class="form-row">
                    <div class="form-group"><label>Código Región:</label><input class="input" type="text" name="codigo_region" value="<?= $codigo_region ?: '+57' ?>"></div>
                    <div class="form-group"><label>Teléfono:</label><input class="input" type="text" name="telefono" value="<?= $telefono ?>"></div>
                </div>
                <button type="submit" class="btn">Actualizar</button>
            </form>

            <!-- Modal Crear Usuario -->
            <div class="modal">
                <div class="modal-content">
                    <h3>Usuario no encontrado</h3>
                    <p>No existe un usuario con ese ID. ¿Desea crearlo?</p>

                    <form method="POST" action="guardar_usuario.php">
                        <input type="hidden" name="accion" value="crear">
                        <input type="hidden" name="fecha_creacion" value="<?= date('Y-m-d H:i:s') ?>">

                        <div class="form-group"><label>Nombre:</label><input class="input" type="text" name="nombre" required></div>
                        <div class="form-group"><label>Apellido:</label><input class="input" type="text" name="apellido" required></div>
                        <div class="form-group"><label>Correo:</label><input class="input" type="email" name="correo" required></div>
                        <div class="form-group"><label>Contraseña:</label><input class="input" type="password" name="contraseña" required></div>
                        <input type="hidden" name="tipo_usuario" value="cliente">


                        <input type="hidden" name="nivel_acceso" value="basico">
                        <div class="form-group"><label>Teléfono:</label><input class="input" type="text" name="telefono"></div>
                        <div class="form-group"><label>Código Región:</label><input class="input" type="text" name="codigo_region" value="+57" readonly></div>

                        <button type="submit" class="btn">Crear Usuario</button>
                        <button type="button" class="btn btn-delete" onclick="document.querySelector('.modal').style.display='none'">Cancelar</button>
                    </form>
                </div>
            </div>
        </section>
    </main>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const formActualizar = document.querySelector('form[action="guardar_usuario.php"][class="form-container"]');

            formActualizar.addEventListener("submit", function(event) {
                const idUsuario = formActualizar.querySelector('input[name="id"]').value.trim();

                if (!idUsuario || idUsuario === "0") {
                    event.preventDefault(); // Evita que se envíe el formulario
                    alert("⚠️ Primero debe realizar una búsqueda de ID de usuario antes de actualizar.");
                }
            });
        });
    </script>

</body>

</html>