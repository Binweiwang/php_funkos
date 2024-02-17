<?php

use services\SessionService;

require_once __DIR__ . '/services/SessionService.php';
$session = SessionService::getInstance();
$username = $session->isLoggedIn() ? $session->getUserName() : 'Invitado';
?>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="/images/favicon.png" alt="Logo" height="30" width="30" class="d-inline-block align-text-top">
                Mis Funkos CRUD
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                    aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link active" href="index.php">Inicio</a>
                    <a class="nav-link" href="category.php">Categorias</a>
                    <a class="nav-link" href="create.php">Añadir Funko</a>
                    <?php if ($session->isLoggedIn()): ?>
                        <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                    <?php else: ?>
                        <a class="nav-link" href="login.php">Iniciar Sesión</a>
                    <?php endif; ?>
                </div>
                <div class="navbar-nav ml-auto">
                    <span class="navbar-text">
                        <?php echo $username; ?>
                    </span>
                </div>
            </div>
        </div>
    </nav>
</header>
