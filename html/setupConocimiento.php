<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title> Configuraciones </title>
</head>
<body>
    <main class="configMain">
        <div style="margin-bottom: 200px">
            <h1> Nivel de conocimiento </h1>
            <h3> Nivel de conocimiento en el que se encuentra</h3>
            <div class="tarjetas">
                <form action="guardarConocimiento.php" method = "post">
                    <input type="hidden" name="conocimiento", value="Principiante">
                    <button type="submit" class="card">
                        <p> Principiante </p>                
                    </button>
                </form>
                <form action="guardarConocimiento.php" method = "post">
                    <input type="hidden" name="conocimiento", value="Intermedio">
                    <button type="submit" class="card">
                        <p> Intermedio </p>                
                    </button>
                </form>
                <form action="guardarConocimiento.php" method = "post">
                    <input type="hidden" name="conocimiento", value="Avanzado">
                    <button type="submit" class="card">
                        <p> Avanzado </p>                
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>