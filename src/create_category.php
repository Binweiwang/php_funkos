<?php
use services\CategoriasService;
use config\Config;
use services\SessionService;

require_once 'vendor/autoload.php';
require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/CategoriasService.php';

$session = SessionService::getInstance();
if (!$session->isLoggedIn() || $session->getUsername() !== 'admin') {
    header('Location: login.php'); // Redirect to login if not admin
    exit;
}

$config = Config::getInstance();
$categoriasService = new CategoriasService($config->db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';

    if (!empty($nombre)) {
        $categoriasService->create($nombre);
        header('Location: category.php');
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Category</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <h1>Create Category</h1>
    <form method="POST">
        <div class="form-group">
            <label for="nombre">Crear categoría</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>
</body>
</html>
