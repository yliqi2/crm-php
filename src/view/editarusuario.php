<?php
require_once __DIR__ . '/../controller/usuario_controller.php';

// id via GET
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
if ($id === null) {
    http_response_code(400);
    echo "Id de usuario no proporcionado.";
    exit;
}

$auth = new UsuarioController();

// Cargar usuario
$usuario = $auth->getUserById($id);
if (!$usuario) {
    http_response_code(404);
    echo "Usuario no encontrado.";
    exit;
}

// Permisos: solo admin o el propio usuario
$currentUserId = isset($_SESSION['id_usuario']) ? (int)$_SESSION['id_usuario'] : null;
if ($currentUserId === null || (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $currentUserId !== $id))) {
    http_response_code(403);
    echo "Acceso denegado.";
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_completo = isset($_POST['nombre_completo']) ? trim($_POST['nombre_completo']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $role = isset($_POST['role']) ? $_POST['role'] : 'vendedor';
    $contra = isset($_POST['contra']) ? $_POST['contra'] : null;
    $contra_confirm = isset($_POST['contra_confirm']) ? $_POST['contra_confirm'] : null;

    if ($nombre_completo === '') {
        $errors[] = 'El nombre completo es obligatorio.';
    } elseif (mb_strlen($nombre_completo) > 100) {
        $errors[] = 'El nombre completo no puede superar 100 caracteres.';
    }

    if ($email === '') {
        $errors[] = 'El email es obligatorio.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'El email no tiene un formato válido.';
    } elseif (mb_strlen($email) > 75) {
        $errors[] = 'El email no puede superar 75 caracteres.';
    }

    // Contraseña es opcional: si se rellena debe coincidir
    if ($contra !== null && $contra !== '') {
        if ($contra !== $contra_confirm) {
            $errors[] = 'Las contraseñas no coinciden.';
        } elseif (mb_strlen($contra) > 100) {
            $errors[] = 'La contraseña no puede superar 100 caracteres.';
        }
    } else {
        $contra = null; // normalizar para el controller
    }

    // Normalizar role
    $role = ($role === 'admin') ? 'admin' : 'vendedor';

    if (empty($errors)) {
        $ok = $auth->updateUser($id, $nombre_completo, $email, $role, $contra);
        if ($ok) {
            header('Location: index.php?action=listausuarios');
            exit;
        } else {
            $errors[] = 'No se pudo actualizar el usuario. Comprueba que el email no esté en uso.';
        }
    }
}

// Valores por defecto para el form (tomar del objeto Usuario)
$nombre_completo = isset($nombre_completo) ? $nombre_completo : $usuario->getNombreCompleto();
$email = isset($email) ? $email : $usuario->getEmail();
$role = isset($role) ? $role : $usuario->getRole();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Editar usuario</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .form { max-width: 520px; margin: 0 auto; }
        input[type="text"], input[type="email"], input[type="password"], select { width: 100%; padding: 8px; margin: 6px 0 12px; box-sizing: border-box; }
        .errors { background:#ffe6e6; padding:10px; border:1px solid #ffb3b3; margin-bottom:12px; }
    </style>
</head>
<body>
    <div class="form">
        <h2>Editar usuario</h2>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <label>Nombre completo
                <input type="text" name="nombre_completo" value="<?php echo htmlspecialchars($nombre_completo, ENT_QUOTES, 'UTF-8'); ?>" maxlength="100" required>
            </label>

            <label>Email
                <input type="email" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" maxlength="75" required>
            </label>

            <label>Nueva contraseña (dejar en blanco para mantener la actual)
                <input type="password" name="contra" maxlength="100">
            </label>

            <label>Confirmar contraseña
                <input type="password" name="contra_confirm" maxlength="100">
            </label>

            <label>Role
                <select name="role">
                    <option value="vendedor" <?php echo ($role === 'vendedor') ? 'selected' : ''; ?>>Vendedor</option>
                    <option value="admin" <?php echo ($role === 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </label>

            <button type="submit">Guardar cambios</button>
        </form>

        <p><a href="index.php?action=listausuarios">Volver al listado de usuarios</a></p>
    </div>
</body>
</html>
