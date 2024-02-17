<?php
use services\CategoriasService;
use config\Config;
use services\SessionService;

// Autoload dependencies and services
require_once 'vendor/autoload.php';
require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/CategoriasService.php';

session_start();
$session = SessionService::getInstance();

if (!$session->isLoggedIn() || $session->getUsername() !== 'admin') {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? '';

$config = Config::getInstance();
$categoriasService = new CategoriasService($config->db);

$category = $categoriasService->findById($id);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $category) {
    $nombre = trim($_POST['nombre'] ?? '');

    if (!empty($nombre)) {
        $categoriasService->update($id, $nombre);
        header('Location: category.php');
    } else {
        $message = 'Category name cannot be empty.';
    }
}

if (!$category) {
    echo "<div class='alert alert-danger' role='alert'>Categoria no encontrado</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Editar categoria</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <h1>Editar categoria</h1>
    <form method="POST">
        <div class="form-group">
            <label for="nombre">Category Name</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($category->nombre) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="category.php" class="btn btn-secondary">Volver a categorias</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
