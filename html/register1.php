<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Registro </title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php if ($_SESSION["error"]) { ?>
      <div>
        <span><?php echo $_SESSION['error']; ?></span>
      </div>
    <?php } ?>
    <form action = "procesarRegistro.php" method="post">
        <h1> Registro </h1>
        <div class="campos">
            <input type="text" name = "nombreUsuario" class = "entrada" placeholder = "Nombre completo">
            <input type="text" name="correoUsuario" class="entrada" placeholder="Correo electronico">
            <input type= "password" name="passwordUsuario" class="entrada" placeholder="Crear contraseña">
            <input type= "password" name="passwordUsuarioConfirm" class="entrada" placeholder="Confirmar contraseña">
        </div>

        <div class="opcionesAdicionales"> 
            <a href="login1.php"> Ya tengo cuenta </a>
        </div>

        <div class="botones">
            <button class="boton"> Registrame </button>
        </div>

    </form>
    
</body>
</html>