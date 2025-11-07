<?php
// Lista de clientes asignados al usuario en sesión
require_once __DIR__ . '/../controller/user_controller.php';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php?action=login');
    exit;
}

$uc = new UserController();

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
    $clientes = $uc->searchClientesByName($searchCliente);
} elseif ($searchEmpresa !== '') {
    $clientes = $uc->searchClientesByEmpresa($searchEmpresa);
} else {
    $clientes = $uc->getClientesForOwner();
}

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
        .btn-edit { display:inline-block; padding:6px 10px; background:#007bff; color:#fff; text-decoration:none; border-radius:4px; }
        .btn-edit:hover { background:#0056b3; }
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
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $c): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($c['id_cliente'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($c['nombre_completo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($c['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($c['tlf'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($c['empresa'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($c['fecha_registro'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a class="btn-edit" href="index.php?action=editarclientes&id=<?php echo urlencode($c['id_cliente']); ?>">Editar</a>
                            <a class="btn-edit" href="index.php?action=oportunidades&id=<?php echo urlencode($c['id_cliente']); ?>">Ver Oportunidades</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p><a href="index.php?action=vendedor">Volver al panel</a></p>
</body>
</html>
