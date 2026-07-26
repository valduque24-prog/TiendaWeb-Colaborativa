<?php
require_once "conexion.php";

$consultaProductos = $conexion->query(
    "SELECT id_producto, nombre, descripcion, precio, stock
     FROM PRODUCTO
     ORDER BY id_producto ASC"
);

$productos = $consultaProductos->fetchAll();

$consultaClientes = $conexion->query(
    "SELECT id_cliente, nombre, email, direccion
     FROM CLIENTE
     ORDER BY id_cliente ASC"
);

$clientes = $consultaClientes->fetchAll();

$consultaCompras = $conexion->query(
    "SELECT
        co.id_compra,
        cl.nombre AS cliente,
        p.nombre AS producto,
        co.cantidad,
        co.total,
        co.fecha
     FROM COMPRA co
     INNER JOIN CLIENTE cl
        ON co.id_cliente = cl.id_cliente
     INNER JOIN PRODUCTO p
        ON co.id_producto = p.id_producto
     ORDER BY co.id_compra ASC"
);

$compras = $consultaCompras->fetchAll();

$mensaje = isset($_GET["mensaje"]) ? $_GET["mensaje"] : "";
$tipoMensaje = isset($_GET["tipo"]) ? $_GET["tipo"] : "";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de compras - Tienda</title>
    <link rel="stylesheet" href="styles.css">
    <script src="validaciones.js?v=2" defer></script>
</head>

<body>

<header>
    <h1>Tienda de Comercio Electrónico</h1>
    <p>Gestión de productos, clientes y compras mediante PHP y MySQL.</p>
</header>

<main>

    <section class="estado-conexion">
        <strong>Conexión establecida correctamente con la base de datos TIENDA.</strong>
    </section>

    <?php if (!empty($mensaje)) { ?>
        <section class="<?php echo $tipoMensaje === "error" ? "mensaje-error" : "mensaje-exito"; ?>">
            <?php echo htmlspecialchars($mensaje); ?>
        </section>
    <?php } ?>

    <nav class="menu">
        <a href="#productos">Productos</a>
        <a href="#clientes">Clientes</a>
        <a href="#compras">Compras</a>
        <a href="consulta_avanzada.php">Consulta avanzada</a>
    </nav>

    <section class="card" id="productos">
        <h2>Registro de productos</h2>

        <form
            action="guardar_producto.php"
            method="POST"
            onsubmit="return validarProducto();">

            <label for="nombre_producto">Nombre del producto:</label>
            <input
                type="text"
                id="nombre_producto"
                name="nombre_producto"
                required>

            <label for="descripcion">Descripción:</label>
            <textarea
                id="descripcion"
                name="descripcion"
                required></textarea>

            <label for="precio">Precio:</label>
            <input
                type="number"
                id="precio"
                name="precio"
                min="1"
                step="0.01"
                required>

            <label for="stock">Stock:</label>
            <input
                type="number"
                id="stock"
                name="stock"
                min="0"
                required>

            <button type="submit">Registrar producto</button>
        </form>

        <h3>Productos registrados</h3>

        <label for="buscarProducto"><strong>Buscar producto:</strong></label>

        <input
            type="text"
            id="buscarProducto"
            placeholder="Escriba el nombre del producto..."
            onkeyup="buscarProducto()">

        <?php if (empty($productos)) { ?>
            <p>No existen productos registrados.</p>
        <?php } else { ?>
            <div class="tabla-contenedor">
                <table id="tablaProductos">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Disponibilidad</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($productos as $producto) { ?>
                            <tr>
                                <td><?php echo $producto["id_producto"]; ?></td>
                                <td><?php echo htmlspecialchars($producto["nombre"]); ?></td>
                                <td><?php echo htmlspecialchars($producto["descripcion"]); ?></td>
                                <td>$<?php echo number_format($producto["precio"], 0, ",", "."); ?></td>
                                <td><?php echo $producto["stock"]; ?></td>
                                <td>
                                    <?php echo $producto["stock"] > 0 ? "Disponible" : "Sin stock"; ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </section>

    <section class="card" id="clientes">
        <h2>Registro de clientes</h2>

        <form
            action="guardar_cliente.php"
            method="POST"
            onsubmit="return validarCliente();">

            <label for="nombre_cliente">Nombre del cliente:</label>
            <input
                type="text"
                id="nombre_cliente"
                name="nombre_cliente"
                required>

            <label for="email">Correo electrónico:</label>
            <input
                type="email"
                id="email"
                name="email"
                required>

            <label for="direccion">Dirección:</label>
            <textarea
                id="direccion"
                name="direccion"
                required></textarea>

            <button type="submit">Registrar cliente</button>
        </form>

        <h3>Clientes registrados</h3>

        <?php if (empty($clientes)) { ?>
            <p>No existen clientes registrados.</p>
        <?php } else { ?>
            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo electrónico</th>
                            <th>Dirección</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($clientes as $cliente) { ?>
                            <tr>
                                <td><?php echo $cliente["id_cliente"]; ?></td>
                                <td><?php echo htmlspecialchars($cliente["nombre"]); ?></td>
                                <td><?php echo htmlspecialchars($cliente["email"]); ?></td>
                                <td><?php echo htmlspecialchars($cliente["direccion"]); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </section>

    <section class="card" id="compras">
        <h2>Registro de compras</h2>

        <?php if (empty($productos) || empty($clientes)) { ?>
            <p>Debe registrar productos y clientes antes de ingresar compras.</p>
        <?php } else { ?>
            <form
                action="guardar_compra.php"
                method="POST"
                onsubmit="return validarCompra();">

                <label for="id_producto">Producto:</label>
                <select id="id_producto" name="id_producto" required>
                    <option value="">Seleccione un producto</option>

                    <?php foreach ($productos as $producto) { ?>
                        <option
                            value="<?php echo $producto["id_producto"]; ?>"
                            data-stock="<?php echo $producto["stock"]; ?>">

                            <?php
                            echo htmlspecialchars($producto["nombre"]) .
                                 " | Stock: " .
                                 $producto["stock"];
                            ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="id_cliente">Cliente:</label>
                <select id="id_cliente" name="id_cliente" required>
                    <option value="">Seleccione un cliente</option>

                    <?php foreach ($clientes as $cliente) { ?>
                        <option value="<?php echo $cliente["id_cliente"]; ?>">
                            <?php echo htmlspecialchars($cliente["nombre"]); ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="cantidad">Cantidad:</label>
                <input
                    type="number"
                    id="cantidad"
                    name="cantidad"
                    min="1"
                    required>

                <button type="submit">Registrar compra</button>
            </form>
        <?php } ?>

        <h3>Compras registradas</h3>

        <?php if (empty($compras)) { ?>
            <p>No existen compras registradas.</p>
        <?php } else { ?>
            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>ID compra</th>
                            <th>Cliente</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($compras as $compra) { ?>
                            <tr>
                                <td><?php echo $compra["id_compra"]; ?></td>
                                <td><?php echo htmlspecialchars($compra["cliente"]); ?></td>
                                <td><?php echo htmlspecialchars($compra["producto"]); ?></td>
                                <td><?php echo $compra["cantidad"]; ?></td>
                                <td>$<?php echo number_format($compra["total"], 0, ",", "."); ?></td>
                                <td><?php echo $compra["fecha"]; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>

        <a class="boton-enlace" href="consulta_avanzada.php">
            Ver clientes con más de dos compras
        </a>
    </section>

</main>

</body>

</html>