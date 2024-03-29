<?php

use config\Config;
use models\Funko;
use services\CategoriasService;
use services\FunkosService;
use services\SessionService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/FunkosService.php';
require_once __DIR__ . '/services/CategoriasService.php';
require_once __DIR__ . '/models/Funko.php';

// Solo se puede modificar si en la sesión el usuario es admin
$session = SessionService::getInstance();
if (!$session->isAdmin()) {
    // No enviar ninguna salida antes de este bloque de código
    echo "<script type='text/javascript'>
            alert('No tienes permisos para modificar un funko');
            window.location.href = 'index.php';
          </script>";
    exit;
}


$config = Config::getInstance();
$categoriasService = new CategoriasService($config->db);
$funkosService = new FunkosService($config->db);

$categorias = $categoriasService->findAll();
$errores = [];
$funko = null;

// Obtenemos el ID del funko a editar
$funkoId = -1;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $funkoId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    // Si no se proporciona un ID, redirigimos al index
    if (!$funkoId) {
        echo "<script type='text/javascript'>
            alert('No se proporcionó un ID de funko');
            window.location.href = 'index.php';
          </script>";
        header('Location: index.php');
        exit;
    }

    // Intentamos obtener el funko por su ID
    try {
        $funko = $funkosService->findById($funkoId);
    } catch (Exception $e) {
        $error = 'Error en el sistema. Por favor intente más tarde.';
    }

    // Si no encontramos el funko, también redirigimos al index
    if (!$funko) {
        header('Location: index.php');
        exit;
    }
}


// Si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Aquí iría el código para procesar los datos del formulario y actualizar el funko
    // Debes asegurarte de validar los datos de la misma manera que en el archivo create.php
    // ...

    // filtramos los datos
//    $marca = filter_input(INPUT_POST, 'marca', FILTER_SANITIZE_STRING);
//    $modelo = filter_input(INPUT_POST, 'modelo', FILTER_SANITIZE_STRING);
//    $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
//    $precio = filter_input(INPUT_POST, 'precio', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
//    $stock = filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_NUMBER_INT);
//    $categoria = filter_input(INPUT_POST, 'categoria', FILTER_SANITIZE_STRING);
//    $funkoId = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $marca = htmlspecialchars(filter_input(INPUT_POST, 'marca'), ENT_QUOTES, 'UTF-8');
    $modelo = htmlspecialchars(filter_input(INPUT_POST, 'modelo'), ENT_QUOTES, 'UTF-8');
    $descripcion = htmlspecialchars(filter_input(INPUT_POST, 'descripcion'), ENT_QUOTES, 'UTF-8');
    $precio = htmlspecialchars(filter_input(INPUT_POST, 'precio'), ENT_QUOTES, 'UTF-8');
    $stock = htmlspecialchars(filter_input(INPUT_POST, 'stock'), ENT_QUOTES, 'UTF-8');
    $categoria = htmlspecialchars(filter_input(INPUT_POST, 'categoria'), ENT_QUOTES, 'UTF-8');
    $funkoId = htmlspecialchars(filter_input(INPUT_POST, 'id'), ENT_QUOTES, 'UTF-8');

    // buscamos el id de la categoria
    $categoria = $categoriasService->findByName($categoria);

    // Validamos los datos
    if (empty($marca)) {
        $errores['marca'] = 'La marca es obligatoria.';
    }

    if (empty($modelo)) {
        $errores['modelo'] = 'El modelo es obligatorio.';
    }

    if (empty($descripcion)) {
        $errores['descripcion'] = 'La descripción es obligatoria.';
    }

    if (!isset($precio) || $precio === '') {
        $errores['precio'] = 'El precio es obligatorio.';
    } elseif ($precio < 0) {
        $errores['precio'] = 'El precio no puede ser negativo.';
    }

    if (!isset($stock) || $stock === '') {
        $errores['stock'] = 'El stock es obligatorio.';
    } elseif ($stock < 0) {
        $errores['stock'] = 'El stock no puede ser negativo.';
    }

    if (empty($categoria)) {
        $errores['categoria'] = 'La categoría es obligatoria.';
    }

    // Si no hay errores, actualizamos el funko
    if (count($errores) === 0) {
        // Actualizamos el funko
        // Creamos el funko
        $funko = new Funko();
        $funko->marca = $marca;
        $funko->modelo = $modelo;
        $funko->descripcion = $descripcion;
        $funko->precio = $precio;
        $funko->stock = $stock;
        $funko->id = $funkoId;
        $funko->categoriaId = $categoria->id;

        try {
            $funkosService->update($funko);
            echo "<script type='text/javascript'>
                alert('funko actualizado correctamente');
                window.location.href = 'index.php';
                </script>";
        } catch (Exception $e) {
            $error = 'Error en el sistema. Por favor intente más tarde.';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Funko</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
          integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link href="/images/favicon.png" rel="icon" type="image/png">
</head>
<body>
<div class="container">
    <?php require_once 'header.php'; ?>
    <h1>Actualizar Funko</h1>

    <form action="update.php" method="post">

        <input type="hidden" name="id" value="<?php echo $funkoId; ?>">

        <div class="form-group">
            <label for="marca">Marca:</label>
            <input class="form-control" id="marca" name="marca" type="text" required
                   value="<?php echo htmlspecialchars($funko->marca); ?>">
            <?php if (isset($errores['marca'])): ?>
                <small class="text-danger"><?php echo $errores['marca']; ?></small>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="modelo">Modelo:</label>
            <input class="form-control" id="modelo" name="modelo" type="text" required
                   value="<?php echo htmlspecialchars($funko->modelo); ?>">
            <?php if (isset($errores['modelo'])): ?>
                <small class="text-danger"><?php echo $errores['modelo']; ?></small>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea class="form-control" id="descripcion" name="descripcion"
                      required><?php echo htmlspecialchars($funko->descripcion); ?></textarea>
            <?php if (isset($errores['descripcion'])): ?>
                <small class="text-danger"><?php echo $errores['descripcion']; ?></small>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="precio">Precio:</label>
            <input class="form-control" id="precio" min="0.0" name="precio" step="0.01" type="number" required
                   value="<?php echo htmlspecialchars($funko->precio); ?>">
            <?php if (isset($errores['precio'])): ?>
                <small class="text-danger"><?php echo $errores['precio']; ?></small>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="imagen">Imagen:</label>
            <input class="form-control" id="imagen" name="imagen" readonly type="text"
                   value="<?php echo htmlspecialchars($funko->imagen); ?>">
        </div>
        <div class="form-group">
            <label for="stock">Stock:</label>
            <input class="form-control" id="stock" min="0" name="stock" type="number" required
                   value="<?php echo htmlspecialchars($funko->stock); ?>">
            <?php if (isset($errores['stock'])): ?>
                <small class="text-danger"><?php echo $errores['stock']; ?></small>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="categoria">Categoría:</label>
            <select class="form-control" id="categoria" name="categoria" required>
                <option value="">Seleccione una categoría</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat->nombre); ?>" <?php if ($cat->nombre == $funko->categoriaNombre) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cat->nombre); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errores['categoria'])): ?>
                <small class="text-danger"><?php echo $errores['categoria']; ?></small>
            <?php endif; ?>
        </div>

        <button class="btn btn-primary" type="submit">Actualizar</button>
        <a class="btn btn-secondary mx-2" href="index.php">Volver</a>
    </form>
</div>

<?php require_once 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct"
        crossorigin="anonymous"></script>
</body>
</html>


