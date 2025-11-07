<?php

require_once __DIR__ . '/../controller/user_controller.php';

$uc = new UserController();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php?action=login');
    exit;
}

$id_cliente = isset($_GET['id']) ? (int) $_GET['id'] : 0;

echo '<p>Oportunidades para el cliente ID: ' . htmlspecialchars($id_cliente, ENT_QUOTES, 'UTF-8') . '</p>';



?>