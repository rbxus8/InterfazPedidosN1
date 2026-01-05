
CREATE TABLE `bodega` (
  `id_bodega` int NOT NULL AUTO_INCREMENT,
  `id_producto` int NOT NULL,
  `cantidad_producto` decimal(10,2) NOT NULL,
  `fecha_almacenamiento` date NOT NULL,
  `id_local` int NOT NULL,
  PRIMARY KEY (`id_bodega`),
  KEY `id_producto` (`id_producto`),
  KEY `id_local` (`id_local`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `categorias` (
  `id_categoria` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `facturas` (
  `id_factura` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int NOT                                                    NULL,
  `id_pedido` int NOT NULL,
  `fecha_emision` date NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'pendiente',
  PRIMARY KEY (`id_factura`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_pedido` (`id_pedido`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `historial_productos` (
  `id_historial` int NOT NULL AUTO_INCREMENT,
  `id_pedido` int NOT NULL,
  `id_producto` int NOT NULL,
  `accion` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_historial`),
  KEY `id_producto` (`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `locales` (
  `id_local` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `direccion` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_local`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `pedidos` (
  `id_pedido` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int DEFAULT NULL,
  `id_local` int NOT NULL,
  `fecha_pedido` datetime NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'pendiente',
  PRIMARY KEY (`id_pedido`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_local` (`id_local`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `productos` (
  `id_producto` int NOT NULL AUTO_INCREMENT,
  `codigo_producto` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `id_categoria` int DEFAULT NULL,
  `fecha_creacion` date NOT NULL,
  `cantidad_stock` decimal(10,2) NOT NULL,
  `unidad_medida` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'disponible',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_producto`),
  UNIQUE KEY `codigo_producto` (`codigo_producto`),
  KEY `id_categoria` (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) DEFAULT NULL,
  `correo` varchar(100) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `tipo_usuario` enum('cliente','empleado') NOT NULL DEFAULT 'cliente',
  `nivel_acceso` enum('basico','medio','admin') DEFAULT 'basico',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `telefono` int DEFAULT NULL,
  `codigo_region` varchar(5) DEFAULT NULL,
  `Apellido` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
