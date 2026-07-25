<?php
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

$nombre = trim($_POST["nombre_producto"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$precio = $_POST["precio"] ?? "";
$stock = $_POST["stock"] ?? "";

if (
    empty($nombre) ||
    empty($descripcion) ||
    !is_numeric($precio) ||
    !is_numeric($stock)
) {
    header(
        "Location: index.php?tipo=error&mensaje=" .
        urlencode("Debe completar correctamente todos los datos del producto.") .
        "#productos"
    );
    exit();
}

$precio = floatval($precio);
$stock = intval($stock);

if ($precio <= 0 || $stock < 0) {
    header(
        "Location: index.php?tipo=error&mensaje=" .
        urlencode("El precio debe ser mayor que cero y el stock no puede ser negativo.") .
        "#productos"
    );
    exit();
}

$sql = "INSERT INTO PRODUCTO (nombre, descripcion, precio, stock)
        VALUES (:nombre, :descripcion, :precio, :stock)";

$consulta = $conexion->prepare($sql);

$consulta->execute([
    ":nombre" => $nombre,
    ":descripcion" => $descripcion,
    ":precio" => $precio,
    ":stock" => $stock
]);

header(
    "Location: index.php?tipo=exito&mensaje=" .
    urlencode("Producto registrado correctamente.") .
    "#productos"
);

exit();
?>