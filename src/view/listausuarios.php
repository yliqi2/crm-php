<?php

if (!isset($_SESSION['id_usuario']) && !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php?action=login');
    exit;
}

require_once __DIR__ . '/../controller/usuario_controller.php';

$uc = new UsuarioController();
$usuarios = $uc->getAllUsers();

if (isset($_GET['removeusuario'])) {
    $id = isset($_GET['removeusuario']) ? (int)$_GET['removeusuario'] : 0;
    $uc->deleteUser($id);
    header('Location: index.php?action=listausuarios');
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de usuarios</title>
</head>
<body>
    
</body>
    <h2>Listado de usuarios</h2>

    <p><a href="index.php?action=crearusuario">Crear usuario</a></p>

    <?php if (empty($usuarios)): ?>
        <p>No hay usuarios para mostrar.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th colspan="2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($u->getIdUsuario(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($u->getNombreCompleto(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($u->getEmail(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($u->getRole(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a href="index.php?action=editarusuario&id=<?php echo urlencode($u->getIdUsuario()); ?>">Editar</a>
                        </td>
                        <td>
                            <a href="index.php?action=listausuarios&removeusuario=<?php echo urlencode($u->getIdUsuario()); ?>">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><a href="index.php?action=admindashboard">Volver al panel de administrador</a></p>
    <?php endif; ?>