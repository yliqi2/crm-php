<?php

require_once __DIR__ . '/../controller/oportunity_controller.php';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php?action=login');
    exit;
}

$oc = new OportunityController();

$id_oportunidad = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id_oportunidad <= 0) {
    header('Location: index.php?action=oportunidades');
    exit;
}

$oportunidad = $oc->getOportunidadById($id_oportunidad);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    $valor_estimado = isset($_POST['valor_estimado']) ? (float)$_POST['valor_estimado'] : 0.0;
    $estado = isset($_POST['estado']) ? trim($_POST['estado']) : '';
    $errors = [];

    if ($titulo === '') $errors[] = 'El título es obligatorio.';
    if ($descripcion === '') $errors[] = 'La descripción es obligatoria.';
    if ($valor_estimado <= 0) $errors[] = 'El valor estimado debe ser mayor que cero.';
    if ($estado === '') $errors[] = 'El estado es obligatorio.';

    if (empty($errors)) {
        $ok = $oc->updateOportunidad($id_oportunidad, $titulo, $descripcion, $valor_estimado, $estado);
        if ($ok) {
            header('Location: index.php?action=oportunidades&idcli=' . urlencode($oportunidad['id_cliente']));
            exit;
        } else {
            $errors[] = 'No se pudo actualizar la oportunidad.';
        }
    } else {
        
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar oportunidades</title>
    <style>
        .btn-edit { display:inline-block; padding:6px 10px; background:#007bff; color:#fff; text-decoration:none; border-radius:4px; }
        .btn-edit:hover { background:#0056b3; }
    </style>

</head>
<body>
    <h2>Editar oportunidad - ID <?php echo htmlspecialchars($id_oportunidad, ENT_QUOTES, 'UTF-8'); ?></h2>
    <p>Aquí iría el formulario para editar la oportunidad con ID <?php echo htmlspecialchars($id_oportunidad, ENT_QUOTES, 'UTF-8'); ?>.</p>
    <p><a class="btn-edit" href="index.php?action=oportunidades&idcli=<?php echo urlencode($id_oportunidad); ?>">Volver al listado de oportunidades</a></p>

    <form method="post">

        <label>Título:
            <input type="text" name="titulo" value="<?php echo htmlspecialchars($oportunidad['titulo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required maxlength="100">
        </label><br><br>

        <label>Descripción:
            <textarea name="descripcion" required maxlength="250"><?php echo htmlspecialchars($oportunidad['descripcion'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </label><br><br>

        <label>Valor Estimado:
            <input type="number" step="0.01" name="valor_estimado" value="<?php echo htmlspecialchars($oportunidad['valor_estimado'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
        </label><br><br>

        <label>Estado:
            <input type="text" name="estado" value="<?php echo htmlspecialchars($oportunidad['estado'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
        </label><br><br>

        <button type="submit">Guardar cambios</button>

    </form>

</body>
</html>