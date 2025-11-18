<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../controller/oportunity_controller.php';
require_once __DIR__ . '/../controller/client_controller.php';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php?action=login');
    exit;
}

$id_cliente = isset($_GET['id_cliente']) ? (int) $_GET['id_cliente'] : 0;

$oc = new OportunityController();
$cc = new ClientController();
$clienteObj = $cc->getCliente($id_cliente);
$clienteNombre = $clienteObj ? $clienteObj->getNombreCompleto() : '';

$errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    $valor_estimado = isset($_POST['valor_estimado']) ? (float) $_POST['valor_estimado'] : 0.0;

    $oportunidad = $oc->insertOportunity($id_cliente, $titulo, $descripcion, $valor_estimado);

    if ($oportunidad) {
        header('Location: index.php?action=listadooportunidades&idcli=' . $id_cliente);
        exit;
    } else {
        $errorMessage = 'Error al crear la oportunidad. Por favor, inténtelo de nuevo.';
    }
}

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Crear Oportunidad</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f6f8fb; }
        .form { max-width: 420px; margin: 0 auto; }
        .card { background: #fff; padding: 16px; border-radius: 6px; border: 1px solid #e6eef8; }
        h1 { margin: 0 0 8px; font-size: 1.25rem; }
        .subtitle { color: #6b7280; margin-bottom: 12px; }
        form { display: block; }
        label { display: block; margin-bottom: 6px; font-weight: 600; }
        input[type="text"], textarea, input[type="number"] { width: 100%; padding: 8px; margin: 6px 0 12px; box-sizing: border-box; border: 1px solid #dbeafe; border-radius: 6px; }
        textarea { min-height: 100px; resize: vertical; }
        .actions { display: flex; gap: 10px; align-items: center; }
        button.primary { background: #2563eb; color: #fff; padding: 8px 12px; border-radius: 6px; border: 0; font-weight: 700; }
        a.back { color: #2563eb; text-decoration: none; }
        .error { color: #ef4444; margin-top: 8px; }
        @media (max-width: 520px) { body { padding: 14px; } }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <h1>Crear Nueva Oportunidad</h1>
            <div class="subtitle">Cliente: <strong><?php echo htmlspecialchars($clienteNombre ?: $id_cliente, ENT_QUOTES, 'UTF-8'); ?></strong></div>

            <?php if ($errorMessage): ?>
                <div class="error"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form method="post">
                <div>
                    <label for="titulo">Título</label>
                    <input type="text" id="titulo" name="titulo" required maxlength="100">
                </div>

                <div>
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" required maxlength="250"></textarea>
                </div>

                <div>
                    <label for="valor_estimado">Valor estimado</label>
                    <input type="number" step="0.01" id="valor_estimado" name="valor_estimado" required>
                </div>

                <div class="actions">
                    <button type="submit" class="primary">Crear Oportunidad</button>
                    <a class="back" href="index.php?action=listadooportunidades&idcli=<?php echo htmlspecialchars($id_cliente, ENT_QUOTES, 'UTF-8'); ?>">Volver al listado</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
