<?php
session_start();
require_once("database.php"); 

if (!isset($_SESSION['idUsuario']) || !isset($_POST['idActividadCompletada'])) {
    header("Location: dashboard.php?status=error_data");
    exit;
}

$idUsuario = $_SESSION['idUsuario'];
$idActividad = $_POST['idActividadCompletada']; 

$sql_check = "SELECT estado FROM actividades WHERE idUsuario = ? AND idActividad = ?";
$stmt_check = $pdo->prepare($sql_check);
$stmt_check->execute([$idUsuario, $idActividad]);
$actividad_actual = $stmt_check->fetch(PDO::FETCH_ASSOC);

if ($actividad_actual && $actividad_actual['estado'] !== 'completado') {
    
    
    $sql1 = "UPDATE actividades SET estado = 'completado' WHERE idUsuario = ? AND idActividad = ?";
    $stmt1 = $pdo->prepare($sql1);
    $stmt1->execute([$idUsuario, $idActividad]);

   
    $sql2 = "UPDATE progreso SET actividadesCompletadas = actividadesCompletadas + 1 WHERE idUsuario = ?";
    $stmt2 = $pdo->prepare($sql2);
    $stmt2->execute([$idUsuario]);

    header("Location: dashboard.php?status=completed");
    exit;

} else {
    header("Location: dashboard.php?status=already_done");
    exit;
}

?>