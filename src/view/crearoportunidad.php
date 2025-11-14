<?php

require_once __DIR__ . '/../controller/oportunity_controller.php';

echo "Crear Oportunidad View";

if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php?action=login');
    exit;
}

$id_cliente = isset($_GET['id_cliente']) ? (int) $_GET['id_cliente'] : 0;

$oc = new OportunityController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    $valor_estimado = isset($_POST['valor_estimado']) ? (float) $_POST['valor_estimado'] : 0.0;
    $estado = isset($_POST['estado']) ? trim($_POST['estado']) : '';

    $oportunidad = $oc->insertOportunity($id_cliente, $titulo, $descripcion, $valor_estimado);

    if ($oportunidad) {
        header('Location: index.php?action=oportunidades&id=' . $id_cliente);
        exit;
    } else {
        echo '<p>Error al crear la oportunidad. Por favor, inténtelo de nuevo.</p>';
    }
}

?>


<h2>Crear Nueva Oportunidad para Cliente ID: <?php echo htmlspecialchars($id_cliente, ENT_QUOTES, 'UTF-8'); ?></h2>
<form method="post">
    <label for="titulo">Título:</label><br>
    <input type="text" id="titulo" name="titulo" required maxlength="100"><br><br>

    <label for="descripcion">Descripción:</label><br>
    <textarea id="descripcion" name="descripcion" required maxlength="250"></textarea><br><br>

    <label for="valor_estimado">Valor Estimado:</label><br>
    <input type="number" step="0.01" id="valor_estimado" name="valor_estimado" required><br><br>

    <button type="submit">Crear Oportunidad</button>
</form>

<a href="index.php?action=oportunidades&id=<?php echo htmlspecialchars($id_cliente, ENT_QUOTES, 'UTF-8'); ?>">Volver al listado de oportunidades</a>
