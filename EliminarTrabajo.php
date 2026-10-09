<?php

session_start();

if (
    !isset($_SESSION["usuario_id"]) ||
    !isset($_SESSION["usuario_tipo"]) ||
    $_SESSION["usuario_tipo"] !== "tecnico"
) {
    header("Location: Iniciarsesion.php");
    exit;
}

$conexion = new mysqli("localhost", "root", "", "Servigo");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$id_tecnico = (int) $_SESSION["usuario_id"];


if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: PerfilTecnico.php");
    exit;
}

$id_trabajo = (int) $_GET["id"];


/* =========================
   ELIMINAR TRABAJO
========================= */

$sql = "DELETE FROM trabajos
        WHERE id = ?
        AND id_tecnico = ?";

$stmt = $conexion->prepare($sql);

if ($stmt === false) {
    die("Error en la consulta: " . $conexion->error);
}

$stmt->bind_param(
    "ii",
    $id_trabajo,
    $id_tecnico
);

$stmt->execute();

$stmt->close();

$conexion->close();


header(
    "Location: PerfilTecnico.php?id=" .
    $id_tecnico
);

exit;

?>