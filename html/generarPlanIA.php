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

$sql_user = "SELECT idioma_deseado, nivel_conocimiento, objetivo FROM usuarios WHERE idUsuario = ?";
$stmt = $pdo->prepare($sql_user);
$stmt->execute([$idUsuario]);   
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) {
    die("Error: No se encontró información del usuario.");
}

$lenguaje = $userData['idioma_deseado'];
$nivel = $userData['nivel_conocimiento'];
$objetivo = $userData['objetivo'];

// Validaciones
if (!$lenguaje || !$nivel || !$objetivo) {
    die("Error: Faltan datos (lenguaje, nivel u objetivo) en tu perfil.");
}

$apiUrl = "https://hierological-unholy-pedro.ngrok-free.dev/api/generate";

$prompt = "
[INST]
Tu rol es actuar como un generador de planes de estudio estricto de aprendizaje de idiomas.
Debes completar la siguiente estructura JSON con EXACTAMENTE 7 temas (1 tema por día), sin errores.
[/INST]

FORMATO OBLIGATORIO:
{
    \"lenguaje\": \"\",
    \"nivel\": \"\",
    \"objetivo\": \"\",
    \"plan\": [
        { \"clave\": \"T1\", \"tema\": \"\" }
        { \"clave\": \"T2\", \"tema\": \"\" },
        { \"clave\": \"T3\", \"tema\": \"\" },
        { \"clave\": \"T4\", \"tema\": \"\" },
        { \"clave\": \"T5\", \"tema\": \"\" },
        { \"clave\": \"T6\", \"tema\": \"\" },
        { \"clave\": \"T7\", \"tema\": \"\" }
    ]
}

Reglas:
- El tema debe ser conciso y ser un título breve.
- La clave SIEMPRE debe ser T1, T2, T3… en orden.
- Solo incluir clave y tema.

Genera el plan para:
Lenguaje: \"$lenguaje\"
Nivel: \"$nivel\"
Objetivo: \"$objetivo\"

INSTRUCCIÓN CRUCIAL: Debes generar TODO el contenido JSON. La lista 'plan' DEBE contener las 7 claves (T1-T7).
SOLO IMPRIME EL OBJETO JSON COMPLETO
";

$body = json_encode([
    "model" => "gemma:2b",
    "prompt" => $prompt,
    "format" => "json",
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
curl_close($ch);

if (!$response) {
    die("Error al conectarse con la API.");
}

$data = json_decode($response, true);

if (!isset($data['response'])) {
    die("Error: Respuesta inesperada del modelo IA.");
}

$raw_ai_response = $data['response']; 

$clean_response = trim($raw_ai_response);

$clean_response = preg_replace('/^\s*```json\s*|\s*```\s*$/', '', $clean_response);
$clean_response = trim($clean_response);

$clean_response = utf8_encode($clean_response);

$plan_json = json_decode($clean_response, true);
if (!isset($plan_json['plan']) || count($plan_json['plan']) !== 7) {
    echo "<p>Valor de \$plan_json: " . print_r($plan_json, true) . "</p>";
    die("<pre><b>Respuesta Cruda de Qwen:</b>\n" . htmlspecialchars($raw_ai_response) . "</pre>");
}

$sql_delete = "DELETE FROM actividades WHERE idUsuario = ?";
$stmt_delete = $pdo->prepare($sql_delete);
$stmt_delete->execute([$idUsuario]);


$sql_insert = "
    INSERT INTO actividades (idUsuario, descripcion, nombreTema, estado)
    VALUES (?, '', ?, 'pendiente')
";
$stmt_insert = $pdo->prepare($sql_insert);

foreach ($plan_json['plan'] as $item) {
    $tema = $item['tema'];
    $stmt_insert->execute([$idUsuario, $tema]);
}

$sql_delete_prog = "DELETE FROM progreso WHERE idUsuario = ?";
$pdo->prepare($sql_delete_prog)->execute([$idUsuario]);

$sql_insert_prog = "
    INSERT INTO progreso (idUsuario, actividadesCompletadas, totalActividades)
    VALUES (?, 0, 7)
";
$pdo->prepare($sql_insert_prog)->execute([$idUsuario]);


header("Location: dashboard.php");
exit;

?>
