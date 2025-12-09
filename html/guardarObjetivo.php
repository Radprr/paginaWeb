<?php
session_start();

require_once("database.php");
if(!isset($_SESSION['idUsuario'])){
    header("Location: setupObjetivo.php");
    exit;
}
if(!isset($_POST['objetivo'])){
    die("No se selecciono ningun nivel de conocimiento");
}
$objetivo = $_POST['objetivo'];
$userId = $_SESSION['idUsuario'];

try{
    $sql = "UPDATE usuarios SET objetivo = ? WHERE idUsuario = ?";
    $stm = $pdo->prepare($sql);
    $stm->execute([$objetivo, $userId]);
    header("Location: generarPlanIA.php");
    exit;
}catch(Exception $e){
    die("Error al guardar: " . $e->getMessage());
}

?>