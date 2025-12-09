<?php
session_start();
require_once("database.php");

if (!isset($_SESSION['idUsuario'])) {
    die("NO_SESSION");
}

$idUsuario = $_SESSION['idUsuario'];
$idActividad = $_POST['idActividad'] ?? 0;

if (!$idActividad) {
    die("ID_ERROR");
}

$sql_check = "SELECT * FROM actividades WHERE id = ? AND idUsuario = ?";
$stmt = $pdo->prepare($sql_check);
$stmt->execute([$idActividad, $idUsuario]);

if (!$stmt->fetch()) {
    die("NOT_OWNER");
}

$sql_update = "UPDATE actividades SET estado = 'completado' WHERE id = ?";
$pdo->prepare($sql_update)->execute([$idActividad]);

$sql_count = "SELECT COUNT(*) FROM actividades WHERE idUsuario = ? AND estado = 'completado'";
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute([$idUsuario]);
$completados = $stmt_count->fetchColumn();

$sql_update_prog = "UPDATE progreso SET actividadesCompletadas = ? WHERE idUsuario = ?";
$pdo->prepare($sql_update_prog)->execute([$completados, $idUsuario]);

echo "OK";
?>
