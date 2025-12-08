<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuraciones</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <main class="configMain">
        <div style="margin-bottom: 200px">
            <h1> Idioma deseado </h1>
            <h3> Idioma que aprenderá en el lapso de los cursos</h3>
            <div class="tarjetas">
                <form action="guardarIdioma.php" method="post">
                    <input type="hidden" name="idioma", value="Inglés">
                    <button type = "submit" class="card">
                        <img src="img/estadosUnidos.png" alt="" class="imgExample">
                        <p> Inglés </p>
                    </button>
                </form>
                <form action="guardarIdioma.php" method = "post">
                    <input type="hidden" name="idioma", value="Japones">
                    <button type= "submit" class="card">
                        <img src="img/Japon-png.png" alt="" class="imgExample">
                        <p> Japones </p>   
                    </button>             
                </form>
                <form action="guardarIdioma.php" method = "post">
                    <input type="hidden" name="idioma", value="Aleman">
                    <button type = "submit" class="card">
                        <img src="img/alemania.png" alt="" class="imgExample">
                        <p> Aleman </p>
                    </button>                
                </form>
            </div>
        </div>
        <a href="setupConocimiento.php">
            <input type="button" value="Siguiente" class="boton">
        </a>
    </main>
</body>
</html>