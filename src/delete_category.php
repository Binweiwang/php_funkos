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

$id = $_GET['id'] ?? null;
if ($id) {
    $config = Config::getInstance();
    $categoriasService = new CategoriasService($config->db);

    if ($categoriasService->isCategoryReferenced($id)) {
        echo "<script type='text/javascript'>
            alert('No se puede eliminar la categoría porque tiene funkos asociados.');
            window.location.href = 'category.php';
          </script>";
        exit;
    } else {
        $categoriasService->delete($id);
    }
}

header('Location: category.php');
exit;