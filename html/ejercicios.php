<?php
session_start();
require_once("database.php"); 

if (!isset($_SESSION['idUsuario'])) {
    header("Location: login1.php");
    exit;
}
/**
 * 
 * @param string $prompt
 * @return array|null 
 */
function call_ai_api($prompt) {
    $apiUrl = "https://hierological-unholy-pedro.ngrok-free.dev/api/generate";

    $body = json_encode([
        "model" => "gemma:2b",
        "prompt" => $prompt,
        "stream" => false
    ]);
    
    set_time_limit(120); 

    $ch = curl_init($apiUrl);

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'ngrok-skip-browser-warning: true'
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200 || !$response) {
        return null; 
    }

    $data = json_decode($response, true);

    if (!isset($data['response'])) {
        return null; 
    }

    $raw_ai_response = $data['response']; 
    
    $clean_response = preg_replace('/^\s*```json\s*|\s*```\s*$/', '', trim($raw_ai_response));
    $clean_response = trim($clean_response);
    
    $quiz_data = json_decode($clean_response, true);

    if (!is_array($quiz_data) || !isset($quiz_data['questions']) || count($quiz_data['questions']) !== 3) {
        error_log("Fallo en la generación del quiz de la IA. Respuesta: " . $raw_ai_response);
        return null; 
    }

    return $quiz_data;
}

$idActividad = $_GET['id'] ?? null;
$key_quiz = "quiz_{$idActividad}"; 

if (!$idActividad || !is_numeric($idActividad)) {
    die("Error: Parámetros de actividad inválidos.");
}

if (!isset($_SESSION['quizzes'][$key_quiz])) {
    
    $sql = "SELECT nombreTema FROM actividades WHERE id = ? AND idUsuario = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idActividad, $_SESSION['idUsuario']]);
    $tema_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tema_data) {
        die("Error: Tema no encontrado o no pertenece al usuario.");
    }

    $nombreTema = $tema_data['nombreTema'];
    $query_tema_limpio = mb_convert_encoding($nombreTema, 'UTF-8', array('UTF-8', 'ISO-8859-1', 'Windows-1252'));

    $prompt_para_ia = "
[INST]
Tu rol es actuar como un generador de cuestionarios de idiomas estricto.
Debes completar la siguiente estructura JSON con EXACTAMENTE 3 preguntas de opción múltiple.
[/INST]

FORMATO OBLIGATORIO:
{
    \"title\": \"\",
    \"questions\": [
        { 
            \"question\": \"\", 
            \"options\": [\"Opción A\", \"Opción B\", \"Opción C\"], 
            \"answer\": \"\" 
        },
        { 
            \"question\": \"\", 
            \"options\": [\"Opción A\", \"Opción B\", \"Opción C\"], 
            \"answer\": \"\" 
        },
        { 
            \"question\": \"\", 
            \"options\": [\"Opción A\", \"Opción B\", \"Opción C\"], 
            \"answer\": \"\" 
        }
    ]
}

Reglas:
- El título debe reflejar el tema y el número de actividad.
- Cada pregunta debe tener entre 2 y 4 opciones de respuesta.
- La clave 'answer' debe coincidir EXACTAMENTE con una de las opciones.
- SOLO IMPRIME EL OBJETO JSON COMPLETO.

Genera el cuestionario para:
Tema principal: '{$query_tema_limpio}'
";
    
    $quiz_data = call_ai_api($prompt_para_ia);

    if (!$quiz_data) {
        $quiz_data = [
            "title" => "FALLBACK: " . $nombreTema . " (Actividad " . $numEjercicio . ")",
            "questions" => [
                ["question" => "FALLBACK 1: ¿Cuál es el saludo básico?", "options" => ["Hola", "Adiós", "Gracias"], "answer" => "Hola"],
                ["question" => "FALLBACK 2: ¿Cuántas actividades hay en este tema?", "options" => ["1", "3", "7"], "answer" => "3"],
                ["question" => "FALLBACK 3: ¿Estás usando el tema '{$nombreTema}'?", "options" => ["Sí", "No"], "answer" => "Sí"],
            ]
        ];
    }
    
    $_SESSION['quizzes'][$key_quiz] = [
        'data' => $quiz_data,
        'index' => 0, 
        'answers' => [], 
        'idActividad' => $idActividad,
        'numEjercicio' => $numEjercicio
    ];
}
$current_quiz_state = &$_SESSION['quizzes'][$key_quiz];
$quiz_data = $current_quiz_state['data'];
$current_index = $current_quiz_state['index'];
$total_questions = count($quiz_data['questions']);

if ($current_index >= $total_questions) {
    header("Location: calificar.php?quiz_key=" . urlencode($key_quiz));
    exit;
}

$current_question = $quiz_data['questions'][$current_index];
$nombreTema = $quiz_data['title']; 

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($nombreTema); ?> (Pregunta <?php echo $current_index + 1; ?>)</title>
    <link rel="stylesheet" href="css/style.css"> 
</head>
<body>
    <header class="headerDashboard">
        <h1><?php echo htmlspecialchars($nombreTema); ?></h1>
    </header>

    <main class="mainDashboard">
        <div class="cuestionarioContenedor">
            <h2>Pregunta <?php echo $current_index + 1; ?> de <?php echo $total_questions; ?></h2>
            
            <form action="siguiente_pregunta.php" method="POST">
                
                <div class="pregunta">
                    <h3><?php echo htmlspecialchars($current_question['question']); ?></h3>
                    
                    <?php 
                    $options = is_array($current_question['options']) ? $current_question['options'] : [];
                    foreach ($options as $option): 
                    ?>
                        <label class="opcion">
                            <input type="radio" 
                                name="respuesta_actual" 
                                value="<?php echo htmlspecialchars($option); ?>" 
                                required>
                            <?php echo htmlspecialchars($option); ?>
                        </label><br>
                    <?php endforeach; ?>
                </div>
                
                <input type="hidden" name="quiz_key" value="<?php echo htmlspecialchars($key_quiz); ?>">
                <input type="hidden" name="question_index" value="<?php echo $current_index; ?>">
                
                <?php if ($current_index < $total_questions - 1): ?>
                    <input type="submit" class="boton"value="Siguiente Pregunta" class="botonSiguiente">
                <?php else: ?>
                    <input type="submit" class="boton" value="Finalizar Quiz" class="botonFinalizar">
                <?php endif; ?>
            </form>
        </div>
    </main>
</body>
</html>