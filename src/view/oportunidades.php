<?php

require_once __DIR__ . '/../controller/oportunity_controller.php';

$oc = new OportunityController();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php?action=login');
    exit;
}

$id_cliente = isset($_GET['id']) ? (int) $_GET['id'] : 0;


$oportunidades = $oc->getOportunidadesByCliente($id_cliente);

echo '<h2>Listado de Oportunidades</h2>';
echo '<a href="index.php?action=listadoclientes">Volver al listado de clientes</a><br><br>';
echo '<a href="index.php?action=crearoportunidad&id_cliente=' . ($id_cliente) . '">Crear nueva oportunidad para este cliente</a><br><br>';

if (empty($oportunidades)) {
    echo '<p>No hay oportunidades para este cliente.</p>';
} else {
    echo '<table border="1" cellpadding="5" cellspacing="0">';
    echo '<tr><th>ID Oportunidad</th><th>ID Cliente</th><th>Título</th><th>Descripción</th><th>Valor Estimado</th><th>Estado</th><th>Fecha de Creación</th><th>Usuario Responsable</th><th>Acciones</th></tr>';
    foreach ($oportunidades as $oportunidad) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($oportunidad['id_oportunidad'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($oportunidad['id_cliente'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($oportunidad['titulo'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($oportunidad['descripcion'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($oportunidad['valor_estimado'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($oportunidad['estado'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($oportunidad['f_creacion'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($oportunidad['usuario_responsable'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '</tr>';
    }
    echo '</table>';
}



?>