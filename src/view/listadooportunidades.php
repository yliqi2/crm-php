<?php
require_once __DIR__ . '/../controller/oportunity_controller.php';
require_once __DIR__ . '/../controller/usuario_controller.php';

$oc = new OportunityController();
$uc = new UsuarioController();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php?action=login');
    exit;
}

$id_cliente = isset($_GET['idcli']) ? (int) $_GET['idcli'] : 0;

$oportunidades = $oc->getOportunidadesByCliente($id_cliente);

if (isset($_GET['delete'])) {
    $id_oportunidad = (int) $_GET['delete'];
    $oc->deleteOportunidad($id_oportunidad);
    header('Location: index.php?action=listadooportunidades&idcli=' . urlencode($id_cliente));
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Oportunidades</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f4f4f4; }
    </style>
</head>
<body>
    <h2>Listado de Oportunidades</h2>
    <h2>Crear Oportunidad para este cliente</h2>
    <p><a class="btn-edit" href="index.php?action=crearoportunidad&id_cliente=<?php echo urlencode($id_cliente); ?>">Crear nueva oportunidad para este cliente</a></p>
    <h2>Oportunidades existentes</h2>
    <?php if (empty($oportunidades)): ?>
        <p>No hay oportunidades para este cliente.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID Oportunidad</th>
                    <th>ID Cliente</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Valor Estimado</th>
                    <th>Estado</th>
                    <th>Fecha de Creación</th>
                    <th>Usuario responsable</th>
                    <th colspan="2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($oportunidades as $oportunidad): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($oportunidad->getIdOportunidad() ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($oportunidad->getIdCliente() ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($oportunidad->getTitulo() ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($oportunidad->getDescripcion() ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($oportunidad->getValor() ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($oportunidad->getEstado() ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($oportunidad->getFCreacion() ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($oportunidad->getUsuarioResponsable() ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a class="btn-edit" href="index.php?action=editaroportunidad&idcli=<?php echo urlencode($id_cliente); ?>&id=<?php echo urlencode($oportunidad->getIdOportunidad()); ?>">Editar</a>
                        </td>
                        <td>
                            <a class="btn-edit" href="index.php?action=listadooportunidades&idcli=<?php echo urlencode($id_cliente); ?>&delete=<?php echo urlencode($oportunidad->getIdOportunidad()); ?>">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <p><a href="index.php?action=listadoclientes">Volver al listado de clientes</a></p>

</body>
</html>


