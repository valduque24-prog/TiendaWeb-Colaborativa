<?php
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

$nombre = trim($_POST["nombre_cliente"] ?? "");
$email = trim($_POST["email"] ?? "");
$direccion = trim($_POST["direccion"] ?? "");

if (
    empty($nombre) ||
    empty($email) ||
    empty($direccion)
) {
    header(
        "Location: index.php?tipo=error&mensaje=" .
        urlencode("Debe completar todos los datos del cliente.") .
        "#clientes"
    );
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header(
        "Location: index.php?tipo=error&mensaje=" .
        urlencode("Debe ingresar un correo electrónico válido.") .
        "#clientes"
    );
    exit();
}

try {
    $sql = "INSERT INTO CLIENTE (nombre, email, direccion)
            VALUES (:nombre, :email, :direccion)";

    $consulta = $conexion->prepare($sql);

    $consulta->execute([
        ":nombre" => $nombre,
        ":email" => $email,
        ":direccion" => $direccion
    ]);

    header(
        "Location: index.php?tipo=exito&mensaje=" .
        urlencode("Cliente registrado correctamente.") .
        "#clientes"
    );
} catch (PDOException $e) {
    header(
        "Location: index.php?tipo=error&mensaje=" .
        urlencode("No fue posible registrar el cliente. Verifique que el correo no esté repetido.") .
        "#clientes"
    );
}

exit();
?>