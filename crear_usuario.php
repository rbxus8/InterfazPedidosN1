<?php
include 'conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id = $_POST['id'] ?? null;
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $codigo_region = $_POST['codigo_region'] ?? '+57';
    $fecha_creacion = $_POST['fecha_creacion'] ?? date('Y-m-d H:i:s');
    $tipo_usuario = $_POST['tipo_usuario'] ?? 'cliente';
    $nivel_acceso = $_POST['nivel_acceso'] ?? 'basico';

    if ($accion === 'crear') {
        // 🔹 Insertar usuario
        $stmt = $conexion->prepare("INSERT INTO usuarios 
            (nombre, Apellido, correo, telefono, codigo_region, fecha_creacion, tipo_usuario, nivel_acceso)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $nombre, $apellido, $correo, $telefono, $codigo_region, $fecha_creacion, $tipo_usuario, $nivel_acceso);
    } elseif ($accion === 'actualizar' && $id) {
        // 🔹 Verificar si el ID existe
        $check = $conexion->prepare("SELECT id FROM usuarios WHERE id=?");
        $check->bind_param("i", $id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            // ✅ Existe → actualizar
            $stmt = $conexion->prepare("UPDATE usuarios 
                SET nombre=?, Apellido=?, correo=?, telefono=?, codigo_region=?,
                    fecha_creacion=?, tipo_usuario=?, nivel_acceso=?
                WHERE id=?");
            $stmt->bind_param("ssssssssi", $nombre, $apellido, $correo, $telefono, $codigo_region, $fecha_creacion, $tipo_usuario, $nivel_acceso, $id);
        } else {
            // ✅ No existe → insertar nuevo
            $stmt = $conexion->prepare("INSERT INTO usuarios 
                (id, nombre, Apellido, correo, telefono, codigo_region, fecha_creacion, tipo_usuario, nivel_acceso)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssssss", $id, $nombre, $apellido, $correo, $telefono, $codigo_region, $fecha_creacion, $tipo_usuario, $nivel_acceso);
        }
    }

    if (isset($stmt) && $stmt->execute()) {
        echo "✅ Usuario guardado correctamente.";
    } else {
        echo "❌ Error SQL: " . $conexion->error;
    }
}
