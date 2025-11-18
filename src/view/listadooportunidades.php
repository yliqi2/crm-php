<?php
require_once __DIR__ . '/../controller/oportunity_controller.php';
require_once __DIR__ . '/../controller/usuario_controller.php';
require_once __DIR__ . '/../controller/client_controller.php';

$oc = new OportunityController();
$uc = new UsuarioController();
$cc = new ClientController();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php?action=login');
    exit;
}

$id_cliente = isset($_GET['idcli']) ? (int) $_GET['idcli'] : 0;


$filter_estado = isset($_GET['filter_estado']) && $_GET['filter_estado'] !== '' ? $_GET['filter_estado'] : null;


if ($filter_estado !== null) {
    if ($id_cliente > 0) {
        $oportunidades = $oc->filtrarClienteEstado($id_cliente, $filter_estado);
    } else {
        $oportunidades = $oc->filtrarEstado($filter_estado);
    }
} else {
    // no filters: use the client context (may be 0)
    $oportunidades = $oc->getOportunidadesByCliente($id_cliente);
}

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
    
</head>
<body>
    <h2>Listado de Oportunidades</h2>
    <h2>Crear Oportunidad para este cliente</h2>
    <p><a class="btn-edit" href="index.php?action=crearoportunidad&id_cliente=<?php echo urlencode($id_cliente); ?>">Crear nueva oportunidad para este cliente</a></p>

    <form method="get">
        <input type="hidden" name="action" value="listadooportunidades">
        <input type="hidden" name="idcli" value="<?php echo htmlspecialchars($id_cliente, ENT_QUOTES, 'UTF-8'); ?>">
        <label>Estado:
            <select name="filter_estado">
                <option value="">Todos</option>
                <?php
                    $estados = ['progreso' => 'Progreso', 'ganada' => 'Ganada', 'perdida' => 'Perdida'];
                    foreach ($estados as $key => $label):
                ?>
                    <option value="<?php echo $key; ?>" <?php echo ($filter_estado !== null && $filter_estado === $key) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Filtrar</button>
          <?php if ($filter_estado !== null): ?>
            <a href="index.php?action=listadooportunidades&idcli=<?php echo urlencode($id_cliente); ?>">Reset</a>
        <?php endif; ?>
    </form>

    <h2>Oportunidades existentes</h2>
    <?php if (empty($oportunidades)): ?>
        <p>No hay oportunidades para este cliente o no cumplen los filtros.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID Oportunidad</th>
                    <th>Cliente</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Valor Estimado</th>
                    <th>Estado</th>
                    <th>Fecha de Creación</th>
                    <th>Usuario responsable</th>
                    <th colspan="3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($oportunidades as $oportunidad): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($oportunidad->getIdOportunidad() ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php
                            $clienteObj = $cc->getCliente($oportunidad->getIdCliente());
                            if ($clienteObj) {
                                echo htmlspecialchars($clienteObj->getNombreCompleto(), ENT_QUOTES, 'UTF-8');
                            } else {
                                echo htmlspecialchars($oportunidad->getIdCliente() ?? '', ENT_QUOTES, 'UTF-8');
                            }
                        ?></td>
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
                        <td>
                            <a class="btn-edit" href="index.php?action=listatareas&idcli=<?php echo urlencode($id_cliente); ?>&ido=<?php echo urlencode($oportunidad->getIdOportunidad()); ?>">Tareas</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <p><a href="index.php?action=listadoclientes">Volver al listado de clientes</a></p>

</body>
</html>


