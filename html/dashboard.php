<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once("database.php"); 

if (!isset($_SESSION['idUsuario'])) {
  header("Location: login1.php");
  exit;
}
$idUsuario = $_SESSION['idUsuario'];


$TOTAL_ACTIVIDADES_PLATAFORMA = 7; 

$sql_progreso = "SELECT * FROM progreso WHERE idUsuario = ?";
$stmt_progreso = $pdo->prepare($sql_progreso);
$stmt_progreso->execute([$idUsuario]);
$progreso = $stmt_progreso->fetch(PDO::FETCH_ASSOC);

if (!$progreso) {
    $sql_insert = "INSERT INTO progreso (idUsuario, actividadesCompletadas, totalActividades) VALUES (?, 0, ?)";
    $stmt_insert = $pdo->prepare($sql_insert);
    $stmt_insert->execute([$idUsuario, $TOTAL_ACTIVIDADES_PLATAFORMA]);
    
    $progreso = [
        'actividadesCompletadas' => 0,
        'totalActividades' => $TOTAL_ACTIVIDADES_PLATAFORMA
    ];
}

$completadas = $progreso['actividadesCompletadas'] ?? 0;
$total = $progreso['totalActividades'] ?? 1; 

if ($total == 0) {
    $total = 1;
}

$porcentaje = round(($completadas / $total) * 100);

$temas_base = [
    ['nombreTema' => 'Saludos y Presentaciones'],
    ['nombreTema' => 'Números y Colores'],
    ['nombreTema' => 'Comida y Bebidas'],
    ['nombreTema' => 'La Familia'],
    ['nombreTema' => 'Verbos Básicos'],
    ['nombreTema' => 'El Tiempo'],
    ['nombreTema' => 'Direcciones'],
];

$sql_count = "SELECT COUNT(*) FROM actividades WHERE idUsuario = ?";
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute([$idUsuario]);
$count = $stmt_count->fetchColumn();

if ($count < count($temas_base)) {
    $sql_insert_act = "INSERT INTO actividades (idUsuario, nombreTema, estado) VALUES (?, ?, 'pendiente')";
    $stmt_insert_act = $pdo->prepare($sql_insert_act);

    foreach ($temas_base as $tema) {
        $stmt_insert_act->execute([$idUsuario, $tema['nombreTema']]);
    }
}

$sql_actividades = "SELECT * FROM actividades WHERE idUsuario = ?";
$stmt_actividades = $pdo->prepare($sql_actividades);
$stmt_actividades->execute([$idUsuario]);
$actividades = $stmt_actividades->fetchAll(PDO::FETCH_ASSOC);


$temas_completados = $completadas; 
$TOTAL_CUADROS_DIAS = 7; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Panel inicial </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="display: flex; flex-direction: column;">
    <header class="headerDashboard">
        <a href="#" class="menu"> </a>
        <h1> Inicio </h1>
        <a href="logout.php" class="user"></a>
    </header>

    <main class="mainDashboard">
        <div style="display: flex; justify-content: center; align-items: center; width: 100%; gap: 20%;">
            
            <div class="barraProgresoContenedor">
                <h2>Progreso</h2>
                
                <div 
                    class="circular" 
                    style="--porcentaje: <?php echo $porcentaje; ?>;"
                >
                    <?php
                        echo $porcentaje . "%"; 
                    ?>
                </div>
            </div>
            
            <div class="diasProgresoContenedor">
                <h2>Días completados</h2>
                <div class="cuadro">
                    <?php
                    for ($i = 1; $i <= $TOTAL_CUADROS_DIAS; $i++) {
                        if ($i <= $temas_completados) {
                            $clase_cuadro = 'cuadroCompletado';
                        } else {
                            $clase_cuadro = 'cuadroNoCompletado';
                        }
                    ?>
                    <div class="<?php echo $clase_cuadro; ?>"> 
                        <span> <?php echo $i; ?> </span>
                    </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>

        <div style="margin-bottom: 600px;"></div>

        <div style="display: flex; justify-content: center; align-items: center; width: 100%; height: 100%;">
            <div class="actividades">
                <?php
                $contadorTema = 1;
                foreach ($actividades as $actividad) {
                    $completada = ($actividad['estado'] === 'completado'); 
                    $claseContenedor = $completada ? 'cuadroActividadCompletado' : 'cuadroActividadNoCompletado';
                    $estadoTexto = $completada ? 'Completado' : 'Pendiente';
                    $iconoClase = $completada ? 'check' : 'clock';
                    $nombreTema = htmlspecialchars($actividad['nombreTema']);
                    $idActividad = $actividad['id']; 
                    ?>
                <div class="actividad">
                    <h2> Tema <?php echo $contadorTema++; ?>: <?php echo $nombreTema; ?></h2> 
                    <div class="<?php echo $claseContenedor; ?>">
                        <div class="textos">
                            <div class="textoActividad">
                                <h3>Actividad 1</h3>
                                <div class="<?php echo $iconoClase; ?>"></div>
                            </div>
                            <div class="textoActividad">
                                <h3>Actividad 2</h3>
                                <div class="<?php echo $iconoClase; ?>"></div>
                            </div>
                            <div class="textoActividad">
                                <h3>Actividad 3</h3>
                                <div class="<?php echo $iconoClase; ?>"></div>
                            </div>
                        </div>
                        <h3><?php echo $estadoTexto; ?></h3>
                    </div>
                    <a href="ejercicios.php">
                        <input type="button" value="Practicar" class="boton">
                    </a>
                </div>
                <?php
                }
                if (empty($actividades)) {
                    echo "<p style='width: 100%; text-align: center;'>Aún no tienes actividades asignadas.</p>";
                }
                ?>
            </div>
        </div>
    </main>
</body>
</html>