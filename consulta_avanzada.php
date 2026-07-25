<?php
require_once "conexion.php";

$sql = "SELECT
            cl.id_cliente,
            cl.nombre,
            cl.email,
            COUNT(co.id_compra) AS cantidad_compras,
            SUM(co.total) AS total_gastado
        FROM CLIENTE cl
        INNER JOIN COMPRA co
            ON cl.id_cliente = co.id_cliente
        GROUP BY
            cl.id_cliente,
            cl.nombre,
            cl.email
        HAVING COUNT(co.id_compra) > 2
        ORDER BY cantidad_compras DESC";

$consulta = $conexion->query($sql);
$resultados = $consulta->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta avanzada de clientes</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

<header>
    <h1>Consulta avanzada de compras</h1>
    <p>Clientes que registran más de dos operaciones de compra.</p>
</header>

<main>

    <section class="card">
        <h2>Clientes frecuentes</h2>

        <p>
            La consulta utiliza <strong>INNER JOIN</strong> para relacionar las tablas
            CLIENTE y COMPRA, <strong>COUNT()</strong> para contabilizar las compras
            realizadas por cada cliente, <strong>GROUP BY</strong> para agrupar los
            resultados y <strong>HAVING</strong> para mostrar únicamente los clientes
            que registran más de dos compras. Además, mediante
            <strong>SUM(total)</strong> calcula el monto total gastado por cada cliente.
        </p>

        <?php if (empty($resultados)) { ?>
            <section class="mensaje-error">
                No existen clientes con más de dos compras registradas.
            </section>
        <?php } else { ?>
            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>ID cliente</th>
                            <th>Nombre</th>
                            <th>Correo electrónico</th>
                            <th>Número de compras</th>
                            <th>Total gastado</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($resultados as $resultado) { ?>
                            <tr>
                                <td><?php echo $resultado["id_cliente"]; ?></td>
                                <td><?php echo htmlspecialchars($resultado["nombre"]); ?></td>
                                <td><?php echo htmlspecialchars($resultado["email"]); ?></td>
                                <td><?php echo $resultado["cantidad_compras"]; ?></td>
                                <td>
                                    $<?php echo number_format($resultado["total_gastado"], 0, ",", "."); ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>

        <a class="boton-enlace" href="index.php">
            Volver a la aplicación
        </a>
    </section>

</main>

</body>

</html>