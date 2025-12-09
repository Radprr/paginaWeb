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

$sql = "SELECT id, nombreTema, estado FROM actividades WHERE idUsuario = ? ORDER BY id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$idUsuario]);
$temas = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temas Generados</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f1f5f9;
            font-family: system-ui, sans-serif;
            padding: 30px;
        }
        h1 {
            font-weight: 700;
            color: #1e293b;
        }
        .tema-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            transition: .2s ease;
        }
        .tema-card:hover {
            transform: translateY(-5px);
        }
        .estado {
            font-size: 13px;
            padding: 6px 10px;
            border-radius: 8px;
            font-weight: 600;
        }
        .pendiente {
            background: #fee2e2;
            color: #b91c1c;
        }
        .completado {
            background: #dcfce7;
            color: #166534;
        }
        .btn-completar {
            margin-top: 10px;
            width: 100%;
        }
    </style>
</head>

<body>

<div class="container">
    <h1 class="mb-4">Temas de tu Plan de Aprendizaje</h1>

    <?php if (empty($temas)): ?>
        <div class="alert alert-warning">
            No tienes temas generados. Crea un plan primero.
        </div>

    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($temas as $index => $t): ?>
                <div class="col-md-4">
                    <div class="tema-card">
                        
                        <h4 class="mb-2">
                            Día <?= $index+1 ?> — <?= htmlspecialchars($t['nombreTema']) ?>
                        </h4>

                        <div class="estado <?= $t['estado'] === 'completado' ? 'completado' : 'pendiente' ?>">
                            <?= ucfirst($t['estado']) ?>
                        </div>

                        <?php if ($t['estado'] !== 'completado'): ?>
                            <button 
                                class="btn btn-success btn-completar" 
                                onclick="marcarCompletado(<?= $t['id'] ?>, this)"
                            >
                                Marcar como completado
                            </button>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-completar" disabled>
                                Ya completado
                            </button>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function marcarCompletado(idActividad, boton) {
    fetch("marcar_completado.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "idActividad=" + idActividad
    })
    .then(res => res.text())
    .then(res => {
        if (res === "OK") {

            boton.outerHTML = `
                <button class="btn btn-secondary btn-completar" disabled>
                    Ya completado
                </button>
            `;

            const estado = boton.parentNode.querySelector(".estado");
            estado.classList.remove("pendiente");
            estado.classList.add("completado");
            estado.innerText = "Completado";

        } else {
            alert("Error: " + res);
        }
    });
}
</script>

</body>
</html>
