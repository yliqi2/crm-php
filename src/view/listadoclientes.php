<?php
// Lista de clientes asignados al usuario en sesión
require_once __DIR__ . '/../controller/client_controller.php';
require_once __DIR__ . '/../controller/usuario_controller.php';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php?action=login');
    exit;
}

$cc = new ClientController();
$uc = new UsuarioController();

// Comprobar si hay búsqueda (GET o POST)
$searchCliente = '';
$searchEmpresa = '';
if (isset($_POST['search']) && trim($_POST['search']) !== '') {
    $searchCliente = trim($_POST['search']);
}
if (isset($_POST['searchEmpresa']) && trim($_POST['searchEmpresa']) !== '') {
    $searchEmpresa = trim($_POST['searchEmpresa']);
}


// Ejecutar búsqueda o listar todos
if ($searchCliente !== '') {
    $clientes = $cc->searchClientesByName($searchCliente);
} elseif ($searchEmpresa !== '') {
    $clientes = $cc->searchClientesByEmpresa($searchEmpresa);
} elseif (isset($_GET['remove'])){
    $idToRemove = (int)$_GET['remove'];
    $cc->removeCliente($idToRemove);
} 
$clientes = $cc->getClientesForOwner();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Listado de clientes</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f4f4f4; }
    </style>
</head>
<body>
    <h2>Mis clientes</h2>

    <form method="post">
        <input type="text" name="search" placeholder="Buscar clientes..." value="<?php echo htmlspecialchars($searchCliente, ENT_QUOTES, 'UTF-8'); ?>" />
        <button type="submit">Buscar</button>
        <?php if ($searchCliente !== ''): ?>
            <a href="index.php?action=listadoclientes" style="margin-left:10px;">Reset</a>
        <?php endif; ?>
    </form>
    <br>
    <form method="post">
        <input type="text" name="searchEmpresa" placeholder="Buscar empresa..." value="<?php echo htmlspecialchars($searchEmpresa, ENT_QUOTES, 'UTF-8'); ?>" />
        <button type="submit">Buscar</button>
        <?php if ($searchEmpresa !== ''): ?>
            <a href="index.php?action=listadoclientes" style="margin-left:10px;">Reset</a>
        <?php endif; ?>
    </form>

    <?php if ($_GET['updated'] ?? false): ?>
        <br>
        <p style="color:green;">Cliente actualizado correctamente.</p>
        <br>
    <?php endif; ?>

    <?php if (empty($clientes)): ?>
        <br>
        <p>No tienes clientes asignados o no se encontraron resultados.</p>
        <br>
    <?php else: ?>
        <br>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Empresa</th>
                    <th>Fecha registro</th>
                    <?php if ($uc->isAdmin((int) $_SESSION['id_usuario'])): ?>
                        <th colspan="3">Acciones</th>
                    <?php else: ?>
                        <th >Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $c): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($c->getIdCliente(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($c->getNombreCompleto(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($c->getEmail(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($c->getTlf(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($c->getEmpresa(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($c->getFechaCreacion(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a href="index.php?action=listadooportunidades&idcli=<?php echo urlencode($c->getIdCliente()); ?>">Ver Oportunidades</a>
                        </td>
                        <?php if ($uc->isAdmin((int) $_SESSION['id_usuario'])): ?>
                            <td>
                            <a href="index.php?action=editarclientes&id=<?php echo urlencode($c->getIdCliente()); ?>">Editar</a>
                            </td>
                            <td>
                            <a href="index.php?action=listadoclientes&remove=<?php echo urlencode($c->getIdCliente()); ?>">Eliminar</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <?php if ($uc->isAdmin((int) $_SESSION['id_usuario'])): ?>
        <p><a href="index.php?action=admindashboard">Volver al panel de administrador</a></p>
    <?php else: ?>
        <p><a href="index.php?action=vendedor">Volver al panel</a></p>
    <?php endif; ?>
</body>
</html>
