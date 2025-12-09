<?php
session_start();
require_once("database.php");

if (!isset($_SESSION['idUsuario'])) {
    header("Location: login1.php");
    exit;
}

$sql_insert_act = "INSERT INTO actividades (idUsuario, nombreTema, descripcion, estado) VALUES (?, '', ?, 'pendiente')";
$stmt_insert_act = $pdo->prepare($sql_insert_act);

$temas_insertados = 0;

foreach ($plan_json['plan'] as $tema) {
    
    $nombreTema_crudo = $tema['tema'];
    $nombreTema_limpio = mb_convert_encoding(
        $nombreTema_crudo, 
        'UTF-8', 
        array('UTF-8', 'ISO-8859-1') 
    );
    
    try {
        $stmt_insert_act->execute([
            $idUsuario, 
            $nombreTema_limpio 
        ]);
        $temas_insertados++;
    } catch (PDOException $e) {
        echo "Error al insertar tema en DB: " . $e->getMessage();
    }
}


if ($temas_insertados > 0) {
    header("Location: dashboard.php");
    exit;
} else {
    echo "Fallo al insertar los temas generados. No se pudo guardar el plan.";
}
?>