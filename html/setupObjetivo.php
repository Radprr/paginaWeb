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
            <h1> Objetivo </h1>
            <h3> Para que objetivo desea aprender el idioma </h3>
            <div class="tarjetas">
                <form action="guardarObjetivo.php" method = "post">
                    <button type="submit" class="card">
                        <input type="hidden" name="objetivo", value="Viajes">
                        <img src="../img/viajes.png" alt="" class="imgExample">
                        <p> Viajes </p>                
                    </button>
                </form>
                <form action="guardarObjetivo.php" method = "post">
                    <button type="submit" class="card">
                        <input type="hidden" name="objetivo", value="Negocios">
                        <img src="img/negocios.png" alt="" class="imgExample">
                        <p> Negocios </p>                
                    </button>
                </form>
                <form action="guardarObjetivo.php" method = "post">
                    <button type="submit" class="card">
                        <input type="hidden" name="objetivo", value="Gramatica">
                        <img src="../img/gramatica.png" alt="" class="imgExample">
                        <p> Gramatica </p>                
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>