<?php
// Vista protegida: mostrar información del usuario en sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php?action=login');
    exit;
}

require_once __DIR__ . '/../controller/usuario_controller.php';
$uc = new UsuarioController();

// gather counts for dashboard
$totalClientes = $uc->countClientes();
$oportunidadesProgreso = $uc->countOportunidadesByState('progreso');
$oportunidadesGanada = $uc->countOportunidadesByState('ganada');
$oportunidadesPerdida = $uc->countOportunidadesByState('perdida');
$tareasPendientes = $uc->countTareasPendientes();

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

    <div class="stats">
        <h3>Resumen del sistema</h3>
        <div>
            <div>
                <strong><?php echo (int)$totalClientes; ?></strong>
                <div>Clientes</div>
            </div>
            <div>
                <strong><?php echo (int)$oportunidadesProgreso; ?></strong>
                <div>Oportunidades (Progreso)</div>
            </div>
            <div>
                <strong><?php echo (int)$oportunidadesGanada; ?></strong>
                <div>Oportunidades (Ganada)</div>
            </div>
            <div>
                <strong><?php echo (int)$oportunidadesPerdida; ?></strong>
                <div>Oportunidades (Perdida)</div>
            </div>
            <div>
                <strong><?php echo (int)$tareasPendientes; ?></strong>
                <div>Tareas pendientes</div>
            </div>
        </div>
    </div>

    <div class="actions">
        <h3>Acciones Rápidas</h3>
        <p><a href="index.php?action=listadoclientes">Gestionar clientes</a></p>
        <p><a href="index.php?action=listausuarios">Gestionar usuarios</a></p>
    </div>
</body>
</html>
