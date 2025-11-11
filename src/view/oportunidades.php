<?php
require_once __DIR__ . '/../controller/oportunity_controller.php';

$oc = new OportunityController();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php?action=login');
    exit;
}

$id_cliente = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$oportunidades = $oc->getOportunidadesByCliente($id_cliente);
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
        .btn-edit { display:inline-block; padding:6px 10px; background:#007bff; color:#fff; text-decoration:none; border-radius:4px; }
        .btn-edit:hover { background:#0056b3; }
    </style>
</head>
<body>
    <h2>Listado de Oportunidades</h2>
    <p><a class="btn-edit" href="index.php?action=listadoclientes">Volver al listado de clientes</a></p>
    <p><a class="btn-edit" href="index.php?action=crearoportunidad&id_cliente=<?php echo urlencode($id_cliente); ?>">Crear nueva oportunidad para este cliente</a></p>

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
                    <th>Usuario Responsable</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($oportunidades as $oportunidad): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($oportunidad['id_oportunidad'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($oportunidad['id_cliente'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($oportunidad['titulo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($oportunidad['descripcion'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($oportunidad['valor_estimado'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($oportunidad['estado'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($oportunidad['f_creacion'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a class="btn-edit" href="index.php?action=editaroportunidad&id=<?php echo urlencode($oportunidad['id_oportunidad']); ?>">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>


