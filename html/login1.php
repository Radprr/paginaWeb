<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Iniciar sesion</title>
</head>
<body>
    <?php if ($_SESSION["error"]) { ?>
      <div>
        <span><?php echo $_SESSION['error']; ?></span>
      </div>
    <?php } ?>
    <form action = "procesarLogin.php" method = "post">
        <h1> Inicio de sesión </h1>

        <div class="campos">
            <input type="text" name="correoUsuario", class="entrada" , placeholder="Correo electronico">
            <input type="password" name="passwordUsuario" , class="entrada" , placeholder="Contraseña">
        </div>

        <div class="opcionesAdicionales">
            <a href=""> ¿Olvidaste tu contraseña? </a>
            <a href="register1.php"> Registrate </a>
        </div>

        <div class="botones">
            <button class="boton"> Ingresar </button>
        </div>

    </form>


</body>
</html>