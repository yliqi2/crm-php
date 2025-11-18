<?php
// Vista protegida: mostrar información del usuario en sesión
if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php?action=login');
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard</title>
    
</head>
<body>
    <div class="box">
        <h2>Panel</h2>
        <p>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre_completo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong></p>
        <p>Email: <?php echo htmlspecialchars($_SESSION['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
        <p>Rol: <?php echo htmlspecialchars($_SESSION['role'] ?? 'vendedor', ENT_QUOTES, 'UTF-8'); ?></p>
        <p><a href="index.php?action=logout">Cerrar sesión</a></p>
    </div>

    <div class="actions">
        <h3>Acciones Rápidas</h3>

        <p><a href="index.php?action=listadoclientes">Gestionar Clientes</a></p>

    </div>
</body>
</html>
