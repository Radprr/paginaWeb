<?php
session_start();
var_dump($_SESSION['idUsuario']);

require_once("database.php");
if(!isset($_SESSION['idUsuario'])){
    header("Location: setupConocimiento.php");
    exit;
}
if(!isset($_POST['conocimiento'])){
    die("No se selecciono ningun nivel de conocimiento");
}
$conocimiento = $_POST['conocimiento'];
$userId = $_SESSION['idUsuario'];

try{
    $sql = "UPDATE usuarios SET nivel_conocimiento = ? WHERE idUsuario = ?";
    $stm = $pdo->prepare($sql);
    $stm->execute([$conocimiento, $userId]);
    header("Location: setupObjetivo.php");
    exit;
}catch(Exception $e){
    die("Error al guardar: " . $e->getMessage());
}

?>