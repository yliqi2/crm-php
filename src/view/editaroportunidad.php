<?php

require_once __DIR__ . '/../controller/oportunity_controller.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php?action=login');
    exit;
}

$oc = new OportunityController();

$id_oportunidad = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$oportunidad = $oc->getOportunidadById($id_oportunidad);

$return_idcli = isset($_GET['idcli']) ? (int)$_GET['idcli'] : (method_exists($oportunidad, 'getIdCliente') ? $oportunidad->getIdCliente() : 0);

// determinar si el usuario puede editar el estado (solo admin)
$canEditEstado = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    $valor_estimado = isset($_POST['valor_estimado']) ? (float)$_POST['valor_estimado'] : 0.0;
    $estado = isset($_POST['estado']) ? trim($_POST['estado']) : ($oportunidad->getEstado() ?? '');
    $errors = [];

    if ($titulo === '') $errors[] = 'El título es obligatorio.';
    if ($descripcion === '') $errors[] = 'La descripción es obligatoria.';
    if ($valor_estimado <= 0) $errors[] = 'El valor estimado debe ser mayor que cero.';
    if ($estado === '') $errors[] = 'El estado es obligatorio.';

    if (empty($errors)) {
        $ok = $oc->updateOportunidad($id_oportunidad, $titulo, $descripcion, $valor_estimado, $estado);
        if ($ok) {
            header('Location: index.php?action=listadooportunidades&idcli=' . urlencode($return_idcli));
            exit;
        } else {
            $errors[] = 'No se pudo actualizar la oportunidad.';
        }
    } else {
        
    }
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar oportunidades</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar oportunidades</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .form { max-width: 420px; margin: 0 auto; }
    input[type="text"], textarea, input[type="number"], select { width: 100%; padding: 8px; margin: 6px 0 12px; box-sizing: border-box; border: 1px solid #dbeafe; border-radius: 6px; }
    textarea { resize: none; }
        .errors { background:#ffe6e6; padding:10px; border:1px solid #ffb3b3; margin-bottom:12px; }
        label { display:block; font-weight:600; }
        button { background:#2563eb;color:#fff;padding:8px 12px;border-radius:6px;border:0;font-weight:700 }
        a.back { color:#2563eb; text-decoration:none; display:inline-block; margin-top:10px }
    </style>
</head>
<body>
    <div class="form">
        <h2>Editar oportunidad - ID <?php echo htmlspecialchars($id_oportunidad, ENT_QUOTES, 'UTF-8'); ?></h2>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post">

            <label for="titulo">Título
                <input id="titulo" type="text" name="titulo" value="<?php echo htmlspecialchars($oportunidad->getTitulo() ?? '', ENT_QUOTES, 'UTF-8'); ?>" required maxlength="100">
            </label>
            <label for="descripcion">Descripción
                <textarea id="descripcion" name="descripcion" required maxlength="250" ><?php echo htmlspecialchars($oportunidad->getDescripcion() ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </label>

            <label for="valor_estimado">Valor estimado
                <input id="valor_estimado" type="number" step="0.01" name="valor_estimado" value="<?php echo htmlspecialchars($oportunidad->getValor() ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </label>
            <label for="estado">Estado
                <?php
                    $currentEstado = $oportunidad->getEstado() ?? '';
                    $estados = ['progreso' => 'progreso', 'ganada' => 'ganada', 'perdida' => 'perdida'];
                ?>
                <select id="estado" name="estado" required <?php echo $canEditEstado ? '' : 'disabled'; ?>>
                    <?php foreach ($estados as $val => $label): ?>
                        <option value="<?php echo htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ((string)$val === (string)$currentEstado) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <div style="margin-top:8px">
                <button type="submit">Guardar cambios</button>
                <a class="back" href="index.php?action=listadooportunidades&idcli=<?php echo urlencode($return_idcli); ?>">Volver al listado de oportunidades</a>
            </div>

        </form>
    </div>

</body>
</html>