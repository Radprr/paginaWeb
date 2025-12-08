<?php
session_start();

require_once('database.php');
if ($_SERVER["REQUEST_METHOD"] === 'POST') {
  if (!isset($_POST['nombreUsuario']) || !isset($_POST['passwordUsuario']) || !isset($_POST['correoUsuario']) ) {
    $_SESSION['error'] = "Todos los campos son obligatorios!";
    header("Location: register1.php");
    exit;
  }
  $nombreUsuario = trim($_POST['nombreUsuario']);
  $passwordUsuario = trim($_POST['passwordUsuario']);
  $correoUsuario = trim($_POST['correoUsuario']);
  //$passwordUsuarioConfirm = trim($_POST['passwordUsuarioConfirm'])

  if (empty($nombreUsuario) || empty($passwordUsuario) || empty($correoUsuario)) {
    $_SESSION['error'] = "Todos los campos son obligatorios.!";
    header("Location: register1.php");
    exit;
  }
  try {
    $hashed = password_hash($passwordUsuario, PASSWORD_BCRYPT);
    $sql = "INSERT INTO usuarios(nombreUsuario, correoUsuario, passwordUsuario) VALUES(?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombreUsuario, $correoUsuario, $hashed]);
    $nuevoId = $pdo->lastInsertId();
    $sqlProgreso = "INSERT INTO progreso (idUsuario) VALUES (?)";
    $stmtProgreso = $pdo->prepare($sqlProgreso);
    $stmtProgreso->execute([$nuevoId]);
    session_start();
    $_SESSION['idUsuario'] = $nuevoId;
    header('Location: setup.php');
    exit;
  } catch (Exception $e) {
    die("Ocurrió un error: " . $e->getMessage());
  }
} else {
  header("Location: register1.php");
  exit;
}