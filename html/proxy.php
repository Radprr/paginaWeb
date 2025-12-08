<?php
header('Content-Type: application/json');
require_once(__DIR__ . '/vendor/autoload.php');

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$inputJSON = file_get_contents('php://input');
$inputData = json_decode($inputJSON, true);



function call_ollama($prompt, $modelo = "qwen-json")
{
  $ngrokUrl = $_ENV["NGROK_URL"];
  $payload = [
    "model" => $modelo,
    "prompt" => $prompt,
    "format" => "json",
    "stream" => false,
  ];


  $ch = curl_init($ngrokUrl);

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'ngrok-skip-browser-warning: true'
  ]);

  $response = curl_exec($ch);
  $error = curl_errno($ch);
  curl_close($ch);

  if ($error) {
    throw new Exception("Error CURL: $error");
  } else {
    $data = json_decode($response, true);

    if (!isset($data["response"])) {
      throw new Exception("Respuesta inválida de Ollama: $response");
    }
    return $data["response"];
  }
}