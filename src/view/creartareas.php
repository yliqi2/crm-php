<?php
require_once __DIR__ . '/../controller/tareas_controller.php';
require_once __DIR__ . '/../controller/oportunity_controller.php';


if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php?action=login');
    exit;
}

// Obtener id de oportunidad desde GET (ido o id)
$id_oportunidad = null;
if (isset($_GET['ido'])) {
    $id_oportunidad = (int) $_GET['ido'];
} elseif (isset($_GET['id_oportunidad'])) {
    $id_oportunidad = (int) $_GET['id_oportunidad'];
} elseif (isset($_POST['id_oportunidad'])) {
    $id_oportunidad = (int) $_POST['id_oportunidad'];
}

if ($id_oportunidad === null) {
    http_response_code(400);
    echo "Id de oportunidad no proporcionado.";
    exit;
}


$op = new OportunityController();
$oportunidad = $op->getOportunidadById($id_oportunidad);
$tc = new TareasController();
$errors = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    $estado = isset($_POST['estado']) ? $_POST['estado'] : 'pendiente';

    if ($descripcion === '') {
        $errors[] = 'La descripción es obligatoria.';
    } elseif (mb_strlen($descripcion) > 250) {
        $errors[] = 'La descripción no puede superar 250 caracteres.';
    }

    $estado = ($estado === 'completada') ? 'completada' : 'pendiente';

    if (empty($errors)) {
        $ok = $tc->insertTarea($id_oportunidad, $descripcion, $estado);
        if ($ok) {
            // Redirigir a la lista de tareas de la oportunidad
            $idcli = isset($_GET['idcli']) ? '&idcli=' . urlencode($_GET['idcli']) : '';
            header('Location: index.php?action=listatareas&ido=' . urlencode($id_oportunidad) . $idcli);
            exit;
        } else {
            $errors[] = 'No se pudo crear la tarea. Comprueba permisos o los datos.';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Crear tarea</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .form { max-width: 520px; margin: 0 auto; }
        textarea, input[type="date"], select { width: 100%; padding: 8px; margin: 6px 0 12px; box-sizing: border-box; }
        .errors { background:#ffe6e6; padding:10px; border:1px solid #ffb3b3; margin-bottom:12px; }
    </style>
</head>
<body>
    <div class="form">
        <h2>Crear tarea para oportunidad #<?php echo htmlspecialchars($oportunidad->getTitulo(), ENT_QUOTES, 'UTF-8'); ?></h2>

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
            <input type="hidden" name="id_oportunidad" value="<?php echo htmlspecialchars($oportunidad->getIdOportunidad(), ENT_QUOTES, 'UTF-8'); ?>">

            <label>Descripción
                <textarea name="descripcion" rows="3" maxlength="250" required style="resize: none"><?php echo isset($descripcion) ? htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
            </label>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <label>Estado
                <select name="estado">
                    <option value="pendiente" <?php echo (isset($estado) && $estado === 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="completada" <?php echo (isset($estado) && $estado === 'completada') ? 'selected' : ''; ?>>Completada</option>
                </select>
            </label>
            <?php else: ?>
                <input type="hidden" name="estado" value="pendiente">
            <?php endif; ?>

            <button type="submit">Crear tarea</button>
        </form>

        <p><a href="index.php?action=listatareas&ido=<?php echo urlencode($id_oportunidad) . (isset($_GET['idcli']) ? '&idcli=' . urlencode($_GET['idcli']) : ''); ?>">Volver</a></p>
    </div>
</body>
</html>
