<?php
include 'conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = $_POST['nombre'] ?? null;
    $codigo_producto = (!empty($_POST['codigo_producto'])) ? $_POST['codigo_producto'] : uniqid('PROD-');
    $stock = $_POST['stock'] ?? null;
    $local = $_POST['local'] ?? null;
    $precio = $_POST['precio'] ?? null;
    $categoria = $_POST['categoria'] ?? null; // id_categoria REAL

    // Validación
    if (
        !empty($nombre) &&
        !empty($codigo_producto) &&
        !empty($categoria) && is_numeric($categoria) &&
        !empty($stock) && is_numeric($stock) && $stock > 0 &&
        !empty($local) && is_numeric($local) &&
        !empty($precio) && is_numeric($precio) && $precio > 0
    ) {

        // INSERT PRODUCTO (✔ columna correcta)
        $query = "
            INSERT INTO productos 
            (codigo_producto, nombre, id_categoria, precio, estado)
            VALUES 
            ('$codigo_producto', '$nombre', $categoria, $precio, 'disponible')
        ";

        if ($conexion->query($query)) {

            $id_producto = $conexion->insert_id;

            // INSERT BODEGA
            $queryStock = "
                INSERT INTO bodega (id_producto, cantidad_producto, id_local)
                VALUES ($id_producto, $stock, $local)
            ";

            if ($conexion->query($queryStock)) {
                echo "Producto agregado correctamente";
            } else {
                echo "Error al agregar stock: " . $conexion->error;
            }
        } else {
            echo "Error al agregar producto: " . $conexion->error;
        }
    } else {
        echo "Por favor ingrese datos válidos.";
    }
}

$conexion->close();
