<?php
session_start();
require_once("database.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (!isset($_POST['correoUsuario']) || !isset($_POST['passwordUsuario'])) {
    $_SESSION['error'] = "Todos los campos son obligatorios!";
    header("Location: login1.php");
    exit;
  }
  $correoUsuario = trim($_POST['correoUsuario']);
  $password = trim($_POST['passwordUsuario']);

  if (empty($correoUsuario) || empty($password)) {
    $_SESSION['error'] = "Todos los campos son obligatorios.!";
    header("Location: login1.php");
    exit;
  }
  try {
    $sql = "SELECT * FROM usuarios WHERE correoUsuario = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$correoUsuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['passwordUsuario'])) {
      $_SESSION["idUsuario"] = $user['idUsuario'];
      $_SESSION["correoUsuario"] = $user["correoUsuario"];
      $_SESSION["nombreUsuario"] = $user["nombreUsuario"];
      header("Location: dashboard.php");
      exit;
    } else {

      $_SESSION['error'] = "Todos los campos son obligatorios.!";
      header("Location: login1.php");
      exit;
    }
    die($user["passwordUsuario"]);
    exit;
  } catch (Exception $e) {

    die("Ocurrió un error " . $e->getMessage());
    exit;
  }
} else {
  header("Location: login1.php");
  exit;
}