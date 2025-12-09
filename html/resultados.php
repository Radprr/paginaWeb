<?php
// resultados.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once("database.php"); 

if (!isset($_SESSION['idUsuario'])) {
    header("Location: login1.php");
    exit;
}

$resultado = $_SESSION['last_quiz_result'] ?? null;

if (!$resultado) {
    header("Location: dashboard.php");
    exit;
}

$aprobado = $resultado['aprobado'];
$clase_resultado = $aprobado ? 'resultado-aprobado' : 'resultado-reprobado';
$mensaje_principal = $aprobado ? '¡Actividad Completada! ✅' : '¡Sigue Practicando! 😔';

unset($_SESSION['last_quiz_result']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultado del Quiz</title>
    <link rel="stylesheet" href="css/style.css"> 
    <style>
        .resultado-aprobado { color: #4CAF50; font-weight: bold; }
        .resultado-reprobado { color: #F44336; font-weight: bold; }
        .resumenPuntaje p { margin: 10px 0; font-size: 1.1em; }
    </style>
</head>
<body>
    <header class="headerDashboard">
        <h1>Resultado del Cuestionario</h1>
    </header>

    <main class="mainDashboard">
        <div class="cuestionarioContenedor">
            <h2>Tema: <?php echo htmlspecialchars($resultado['nombreTema']); ?></h2>
            
            <h3 class="<?php echo $clase_resultado; ?>" style="font-size: 1.8em; margin: 20px 0;">
                <?php echo $mensaje_principal; ?>
            </h3>
            
            <div class="resumenPuntaje">
                <p>Preguntas Correctas: <strong><?php echo $resultado['correctas']; ?></strong> de <strong><?php echo $resultado['total']; ?></strong></p>
                <p>Puntuación Final: <strong><?php echo $resultado['porcentaje']; ?>%</strong></p>
                
                <?php if ($aprobado): ?>
                    <p style="color: #4CAF50;">Tu progreso ha sido actualizado en la plataforma.</p>
                <?php else: ?>
                    <p style="color: #F44336;">Necesitas obtener un 66% o más para completar la actividad.</p>
                <?php endif; ?>
            </div>
            
            <div style="margin-top: 30px;">
                <a href="dashboard.php" class="botonSiguiente" style="padding: 10px 20px;">Volver a Actividades</a>
            </div>

        </div>
    </main>
</body>
</html>