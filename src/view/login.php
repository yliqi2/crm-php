<?php
require_once __DIR__ . '/../controller/auth_controller.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $contra = isset($_POST['contra']) ? $_POST['contra'] : '';

    if ($email === '') {
        $errors[] = 'El email es obligatorio.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Formato de email inválido.';
    }

    if ($contra === '') {
        $errors[] = 'La contraseña es obligatoria.';
    }

    if (empty($errors)) {
        $auth = new AuthController();
        $usuario = $auth->login($email, $contra);
        if ($usuario) {
            // Iniciar sesión y guardar datos mínimos
            session_regenerate_id(true);
            $_SESSION['id_usuario'] = $usuario->getIdUsuario();
            $_SESSION['nombre_completo'] = $usuario->getNombreCompleto();
            $_SESSION['email'] = $usuario->getEmail();
            // Nuevo esquema: role ('admin'|'vendedor')
            $_SESSION['role'] = $usuario->getRole();
            // Mantener isAdmin boolean por compatibilidad
            $_SESSION['isAdmin'] = $usuario->isAdmin();
            
            if ($_SESSION['role'] === 'admin') {
                header('Location: index.php?action=admindashboard');
            } else {
                header('Location: index.php?action=vendedor');
            }
            
            exit;
        } else {
            $errors[] = 'Email o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .form { max-width: 420px; margin: 0 auto; }
        input[type="email"], input[type="password"] { width: 100%; padding: 8px; margin: 6px 0 12px; box-sizing: border-box; }
        .errors { background:#ffe6e6; padding:10px; border:1px solid #ffb3b3; margin-bottom:12px; }
    </style>
</head>
<body>
    <div class="form">
        <h2>Iniciar sesión</h2>

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
            <label>Email
                <input type="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email, ENT_QUOTES, 'UTF-8') : ''; ?>" maxlength="75" required>
            </label>

            <label>Contraseña
                <input type="password" name="contra" maxlength="100" required>
            </label>

            <button type="submit">Entrar</button>
        </form>

    <p>¿No tienes cuenta? <a href="index.php?action=register">Regístrate</a></p>
    </div>
</body>
</html>
