<?php
session_start();
var_dump($_SESSION['idUsuario']);

require_once("database.php");
if(!isset($_SESSION['idUsuario'])){
    header("Location: setup2.php");
    exit;
}
if(!isset($_POST['idioma'])){
    die("No se selecciono ningun idioma");
}
$idioma = $_POST['idioma'];
$userId = $_SESSION['idUsuario'];

try{
    $sql = "UPDATE usuarios SET idioma_deseado = ? WHERE idUsuario = ?";
    $stm = $pdo->prepare($sql);
    $stm->execute([$idioma, $userId]);
    header("Location: setupConocimiento.php");
    exit;
}catch(Exception $e){
    die("Error al guardar: " . $e->getMessage());
}

?>