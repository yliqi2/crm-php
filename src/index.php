<?php
// Router simple MVC: muestra vistas según ?action=...
session_start();

// Acción permitida y mapeo a vistas
$allowed = ['login', 'register', 'admindashboard', 'vendedor', 'listadoclientes', 'editarclientes', 'oportunidades', 'editaroportunidad', 'crearoportunidad', 'logout'];
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
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"], $params["secure"], $params["httponly"]
        );
    }
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
