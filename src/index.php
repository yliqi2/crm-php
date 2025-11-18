<?php
// Router simple MVC: muestra vistas según ?action=...
session_start();

// Acción permitida y mapeo a vistas
$allowed = ['login', 'register', 'admindashboard', 'listausuarios', 'crearusuario', 'editarusuario','vendedor', 'listadoclientes', 'editarclientes', 'listadooportunidades', 'editaroportunidad', 'crearoportunidad','listatareas', 'creartareas', 'logout'];
$action = isset($_GET['action']) ? basename($_GET['action']) : 'login';

// Si action no esta dentor del array permitido de allowed muestra un error 404 (INEXISTENTE)
if (!in_array($action, $allowed, true)) {
    http_response_code(404);
    echo "404 - Página no encontrada";
    exit;
}

// Logout: destruir sesión y redirigir a login
if ($action === 'logout') {
    $_SESSION = [];
    session_destroy();
    header('Location: index.php?action=login');
    exit;
}

$viewFile = __DIR__ . '/view/' . $action . '.php';
if (!file_exists($viewFile)) {
    http_response_code(404);
    echo "404 - Vista no encontrada";
    exit;
}

// Cargar la vista
require $viewFile;
