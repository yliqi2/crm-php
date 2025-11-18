<?php
// Formulario de edición de cliente (solo propietario)
require_once __DIR__ . '/../controller/client_controller.php';
require_once __DIR__ . '/../model/cliente.php';

$cc = new ClientController();

// Obtener id desde GET o POST
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php?action=listadoclientes');
    exit;
}



// Procesar POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_completo = isset($_POST['nombre_completo']) ? trim($_POST['nombre_completo']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $tlf = isset($_POST['tlf']) ? trim($_POST['tlf']) : '';
    $empresa = isset($_POST['empresa']) ? trim($_POST['empresa']) : '';

    $errors = [];
    if ($nombre_completo === '') $errors[] = 'El nombre es obligatorio.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';
    if ($tlf === '') $errors[] = 'El teléfono es obligatorio.';
    if ($empresa === '') $errors[] = 'La empresa es obligatoria.';
    // Excluir el cliente actual al comprobar existencia de email (evita falso positivo si no se cambia)
    if ($cc->emailAlreadyInUse($email, $id)) $errors[] = 'El email ya está en uso.';

    if (empty($errors)) {
        $ok = $cc->modifyCliente($id, $nombre_completo, $email, $tlf, $empresa);
        if ($ok) {
            header('Location: index.php?action=listadoclientes&updated=1');
            exit;
        } else {
            $errors[] = 'No se pudo actualizar el cliente.';
        }
    }
} else {
    
    $cliente = $cc->getClienteIfOwner($id);

    // precargar valores
    $nombre_completo = $cliente->getNombreCompleto();
    $email = $cliente->getEmail();
    $tlf = $cliente->getTlf();
    $empresa = $cliente->getEmpresa();
    $errors = [];
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Editar cliente</title>
    <style>
        body{font-family:Arial, sans-serif;padding:20px}
        .form{max-width:520px}
        input[type=text], input[type=email]{width:100%;padding:8px;margin:6px 0}
        .errors{background:#ffe6e6;padding:10px;border:1px solid #ffb3b3;margin-bottom:12px}
        .success{background:#e6ffea;padding:10px;border:1px solid #b3ffcf;margin-bottom:12px}
    </style>
</head>
<body>
    <div class="form">
        <h2>Editar cliente</h2>

        <?php if (!empty($errors)): ?>
            <div class="errors"><ul><?php foreach($errors as $err){ echo '<li>'.htmlspecialchars($err, ENT_QUOTES, 'UTF-8').'</li>'; } ?></ul></div>
        <?php endif; ?>

        <form method="post" action="">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
            <label>Nombre completo
                <input type="text" name="nombre_completo" value="<?php echo htmlspecialchars($nombre_completo, ENT_QUOTES, 'UTF-8'); ?>" maxlength="100" required>
            </label>
            <label>Email
                <input type="email" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" maxlength="75" required>
            </label>
            <label>Teléfono
                <input type="text" name="tlf" value="<?php echo htmlspecialchars($tlf, ENT_QUOTES, 'UTF-8'); ?>" maxlength="15" required>
            </label>
            <label>Empresa
                <input type="text" name="empresa" value="<?php echo htmlspecialchars($empresa, ENT_QUOTES, 'UTF-8'); ?>" maxlength="100" required>
            </label>

            <button type="submit">Guardar</button>
            <a href="index.php?action=listadoclientes">Cancelar</a>
        </form>
    </div>
</body>
</html>