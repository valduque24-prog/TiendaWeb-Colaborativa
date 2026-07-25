<?php
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

$idProducto = intval($_POST["id_producto"] ?? 0);
$idCliente = intval($_POST["id_cliente"] ?? 0);
$cantidad = intval($_POST["cantidad"] ?? 0);

if ($idProducto <= 0 || $idCliente <= 0 || $cantidad <= 0) {
    header(
        "Location: index.php?tipo=error&mensaje=" .
        urlencode("Debe seleccionar un producto, un cliente y una cantidad válida.") .
        "#compras"
    );
    exit();
}

$consultaProducto = $conexion->prepare(
    "SELECT id_producto, nombre, precio, stock
     FROM PRODUCTO
     WHERE id_producto = :id_producto"
);

$consultaProducto->execute([
    ":id_producto" => $idProducto
]);

$producto = $consultaProducto->fetch();

$consultaCliente = $conexion->prepare(
    "SELECT id_cliente
     FROM CLIENTE
     WHERE id_cliente = :id_cliente"
);

$consultaCliente->execute([
    ":id_cliente" => $idCliente
]);

$cliente = $consultaCliente->fetch();

if (!$producto || !$cliente) {
    header(
        "Location: index.php?tipo=error&mensaje=" .
        urlencode("El producto o cliente seleccionado no existe.") .
        "#compras"
    );
    exit();
}

if ($producto["stock"] < $cantidad) {
    header(
        "Location: index.php?tipo=error&mensaje=" .
        urlencode("No existe stock suficiente para registrar la compra.") .
        "#compras"
    );
    exit();
}

$total = $producto["precio"] * $cantidad;
$fecha = date("Y-m-d");

$insertarCompra = $conexion->prepare(
    "INSERT INTO COMPRA
        (cantidad, total, fecha, id_producto, id_cliente)
     VALUES
        (:cantidad, :total, :fecha, :id_producto, :id_cliente)"
);

$insertarCompra->execute([
    ":cantidad" => $cantidad,
    ":total" => $total,
    ":fecha" => $fecha,
    ":id_producto" => $idProducto,
    ":id_cliente" => $idCliente
]);

$nuevoStock = $producto["stock"] - $cantidad;

$actualizarStock = $conexion->prepare(
    "UPDATE PRODUCTO
     SET stock = :stock
     WHERE id_producto = :id_producto"
);

$actualizarStock->execute([
    ":stock" => $nuevoStock,
    ":id_producto" => $idProducto
]);

header(
    "Location: index.php?tipo=exito&mensaje=" .
    urlencode("Compra registrada correctamente y stock actualizado.") .
    "#compras"
);

exit();
?>