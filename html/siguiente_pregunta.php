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

$quiz_key = $_POST['quiz_key'] ?? null;
$question_index = $_POST['question_index'] ?? null;
$respuesta_usuario = $_POST['respuesta_actual'] ?? null;

if (!$quiz_key || !is_numeric($question_index) || !isset($_SESSION['quizzes'][$quiz_key])) {
    die("Error de validación: Faltan datos del quiz o la sesión es inválida.");
}

if ($respuesta_usuario === null) {
    $current_quiz_state = $_SESSION['quizzes'][$quiz_key];
    $idActividad = $current_quiz_state['idActividad'];
    $numEjercicio = $current_quiz_state['numEjercicio'];
    
    header("Location: ejercicios.php?id=" . $idActividad . "&ejercicio=" . $numEjercicio . "&error=no_respuesta");
    exit;
}

$current_quiz_state = &$_SESSION['quizzes'][$quiz_key];
$quiz_data = $current_quiz_state['data'];
$current_index = $current_quiz_state['index'];
$total_questions = count($quiz_data['questions']);

if ((int)$question_index !== $current_index) {
    $question_index = $current_index;
}

if ($current_index < $total_questions) {
    $correct_answer = $quiz_data['questions'][$current_index]['answer'];
    
    $current_quiz_state['answers'][$current_index] = [
        'user_answer' => $respuesta_usuario,
        'correct_answer' => $correct_answer,
        'is_correct' => ($respuesta_usuario === $correct_answer)
    ];
}


$current_quiz_state['index']++; 

$next_index = $current_quiz_state['index'];

if ($next_index < $total_questions) {
    $idActividad = $current_quiz_state['idActividad'];
    $numEjercicio = $current_quiz_state['numEjercicio'];
    header("Location: ejercicios.php?id=" . $idActividad . "&ejercicio=" . $numEjercicio);
    exit;
} else {
    
    $preguntas_correctas = 0;
    foreach ($current_quiz_state['answers'] as $answer) {
        if ($answer['is_correct']) {
            $preguntas_correctas++;
        }
    }

    $puntaje_porcentaje = ($total_questions > 0) ? round(($preguntas_correctas / $total_questions) * 100) : 0;
    $umbral_aprobacion = 66;
    $aprobado = ($puntaje_porcentaje >= $umbral_aprobacion);

    if ($aprobado) {
        $idUsuario = $_SESSION['idUsuario'];
        $idActividad = $current_quiz_state['idActividad'];
        
        $sql_update_actividad = "UPDATE actividades SET estado = 'completado' WHERE id = ? AND idUsuario = ? AND estado != 'completado'";
        $stmt_actividad = $pdo->prepare($sql_update_actividad);
        $stmt_actividad->execute([$idActividad, $idUsuario]);

        if ($stmt_actividad->rowCount() > 0) {
            $sql_update_progreso = "UPDATE progreso SET actividadesCompletadas = actividadesCompletadas + 1 WHERE idUsuario = ?";
            $pdo->prepare($sql_update_progreso)->execute([$idUsuario]);
        }
    }
    
    $_SESSION['last_quiz_result'] = [
        'aprobado' => $aprobado,
        'correctas' => $preguntas_correctas,
        'total' => $total_questions,
        'porcentaje' => $puntaje_porcentaje,
        'nombreTema' => $quiz_data['title'] ?? 'Cuestionario Finalizado'
    ];

    unset($_SESSION['quizzes'][$quiz_key]);

    header("Location: resultados.php");
    exit;
}
?>