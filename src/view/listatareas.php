<?php
require_once __DIR__ . '/../controller/tareas_controller.php';
require_once __DIR__ . '/../controller/oportunity_controller.php';


if (!isset($_SESSION['id_usuario'])) {
	header('Location: index.php?action=login');
	exit;
}

// id de oportunidad puede venir como id_oportunidad o id
$id_oportunidad = null;
if (isset($_GET['ido'])) {
	$id_oportunidad = (int) $_GET['ido'];
    $oc = new OportunityController();
    $oportunidad = $oc->getOportunidadById($id_oportunidad);
} 
if (isset($_GET['completar'])) {
    $id_tarea = (int) $_GET['completar'];
    $tc = new TareasController();
    $tc->completarTarea($id_tarea);
    header('Location: index.php?action=listatareas&ido=' . urlencode($id_oportunidad) . (isset($_GET['idcli']) ? '&idcli=' . urlencode($_GET['idcli']) : ''));
    exit;
}

if ($id_oportunidad === null) {
	//nohay id entonces no se hace nada 
	exit;
}

$tc = new TareasController();
$tareas = $tc->getTareasByOportunidad($id_oportunidad);



?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Listado de tareas</title>
	<style>
		body { font-family: Arial, sans-serif; padding: 20px; }
		table { border-collapse: collapse; width: 100%; }
		th, td { border: 1px solid #ddd; padding: 8px; }
		th { background: #f4f4f4; }
	</style>
</head>
<body>
	<h2>Tareas de la oportunidad #<?php echo htmlspecialchars($oportunidad->getTitulo(), ENT_QUOTES, 'UTF-8'); ?></h2>
	<?php $idcliParam = isset($_GET['idcli']) ? '&idcli=' . urlencode($_GET['idcli']) : ''; ?>
	<p><a class="btn-edit" href="index.php?action=creartareas&ido=<?php echo urlencode($id_oportunidad) . $idcliParam; ?>">Crear nueva tarea para esta oportunidad</a></p>
	<?php if (empty($tareas)): ?>
		<p>No hay tareas para esta oportunidad.</p>
	<?php else: ?>
		<table>
			<thead>
				<tr>
					<th>ID</th>
					<th>Descripción</th>
					<th>Fecha</th>
					<th>Estado</th>
                    <th>Acciones</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($tareas as $t): ?>
					<tr>
						<td><?php echo htmlspecialchars($t->getIdTarea(), ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlspecialchars($t->getDescripcion(), ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlspecialchars($t->getFecha(), ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlspecialchars($t->getEstado(), ENT_QUOTES, 'UTF-8'); ?></td>
						<td>
							<?php if ($t->getEstado() === 'completada'): ?>
								No hay acciones disponibles
							<?php else: ?>
								<a href="index.php?action=listatareas&ido=<?php echo urlencode($id_oportunidad) . $idcliParam; ?>&completar=<?php echo urlencode($t->getIdTarea()); ?>">Completar</a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>

	<p><a href="index.php?action=listadooportunidades&idcli=<?php echo urlencode($_GET['idcli'] ?? ''); ?>">Volver</a></p>
</body>
</html>