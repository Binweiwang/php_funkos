<?php
use services\CategoriasService;
use services\SessionService;
use config\Config;

// Autoload classes
require_once 'vendor/autoload.php';

// Include session, configuration, and necessary services
require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/CategoriasService.php';

// Start or resume a session
$session = SessionService::getInstance();

// Check if the user is logged in and if they are an admin
$isAdmin = $session->isLoggedIn() && $session->getUsername() === 'admin';

// Create a Config instance to get database details
$config = Config::getInstance();

// Instantiate the CategoriasService with the required database connection or configuration details
$categoriasService = new CategoriasService($config->db);

// Fetch all categories
$categories = $categoriasService->findAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Categories</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <?php require_once 'header.php';?>
    <h1>Categories</h1>
    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Created At</th>
            <?php if ($isAdmin): ?>
                <th>Actions</th>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($categories as $category): ?>
            <tr>
                <td><?= htmlspecialchars($category->id); ?></td>
                <td><?= htmlspecialchars($category->nombre); ?></td>
                <td><?= htmlspecialchars($category->createdAt ?? 'N/A'); ?></td>
                <?php if ($isAdmin): ?>
                    <td>
                        <a href="edit_category.php?id=<?= $category->id; ?>" class="btn btn-secondary btn-sm">Edit</a>
                        <a href="delete_category.php?id=<?= $category->id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Estas seguro eliminar?');">Eliminar</a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($isAdmin): ?>
        <a class="btn btn-success" href="create_category.php">Crear categoría</a>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
