<?php
require_once __DIR__ . '/../controller/usuario_controller.php';

$errors = [];
$success = false;

if (!isset($_SESSION['id_usuario']) && !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php?action=login');
    exit;
}

$uc = new UsuarioController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_completo = isset($_POST['nombre_completo']) ? trim($_POST['nombre_completo']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $contra = isset($_POST['contra']) ? $_POST['contra'] : '';
    $contra_confirm = isset($_POST['contra_confirm']) ? $_POST['contra_confirm'] : '';
    $role = isset($_POST['role']) ? $_POST['role'] : 'vendedor';

    // Validaciones
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

    if ($contra === '') {
        $errors[] = 'La contraseña es obligatoria.';
    } elseif (mb_strlen($contra) > 100) {
        $errors[] = 'La contraseña no puede superar 100 caracteres.';
    }

    if ($contra !== $contra_confirm) {
        $errors[] = 'Las contraseñas no coinciden.';
    }

    // Normalizar role
    $role = ($role === 'admin') ? 'admin' : 'vendedor';

    if (empty($errors)) {
        $usuario = $uc->createUser($nombre_completo, $email, $contra, $role);
        header('Location: index.php?action=listausuarios');
        exit;
        
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Crear usuario</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .form { max-width: 520px; margin: 0 auto; }
        input[type="text"], input[type="email"], input[type="password"], select { width: 100%; padding: 8px; margin: 6px 0 12px; box-sizing: border-box; }
        .errors { background:#ffe6e6; padding:10px; border:1px solid #ffb3b3; margin-bottom:12px; }
    </style>
</head>
<body>
    <div class="form">
        <h2>Crear usuario</h2>

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
                <input type="text" name="nombre_completo" value="<?php echo isset($nombre_completo) ? htmlspecialchars($nombre_completo, ENT_QUOTES, 'UTF-8') : ''; ?>" maxlength="100" required>
            </label>

            <label>Email
                <input type="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email, ENT_QUOTES, 'UTF-8') : ''; ?>" maxlength="75" required>
            </label>

            <label>Contraseña
                <input type="password" name="contra" maxlength="100" required>
            </label>

            <label>Confirmar contraseña
                <input type="password" name="contra_confirm" maxlength="100" required>
            </label>

            <label>Role
                <select name="role">
                    <option value="vendedor" <?php echo (isset($role) && $role === 'vendedor') ? 'selected' : ''; ?>>Vendedor</option>
                    <option value="admin" <?php echo (isset($role) && $role === 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </label>

            <button type="submit">Crear usuario</button>
        </form>

        <p><a href="index.php?action=listausuarios">Volver al listado de usuarios</a></p>
    </div>
</body>
</html>
