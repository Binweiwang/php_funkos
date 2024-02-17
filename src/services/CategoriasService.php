<?php

namespace services;

use models\Categoria;
use PDO;
use function Antikirra\uuid4;

require_once __DIR__ . '/../models/Categoria.php';

class CategoriasService
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categorias ORDER BY id ASC");
        $stmt->execute();

        $categorias = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categoria = new Categoria(
                $row['id'],
                $row['nombre'],
                $row['created_at'],
                $row['updated_at'],
                $row['is_deleted']
            );
            $categorias[] = $categoria;
        }
        return $categorias;
    }

    public function findByName($name)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categorias WHERE nombre = :nombre");
        $stmt->execute(['nombre' => $name]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return false;
        }
        $categoria = new Categoria(
            $row['id'],
            $row['nombre'],
            $row['created_at'],
            $row['updated_at'],
            $row['is_deleted']
        );
        return $categoria;
    }
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM categorias WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public function isCategoryReferenced($categoryId)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM funkos WHERE categoria_id = :categoriaId");
        $stmt->execute(['categoriaId' => $categoryId]);
        $count = $stmt->fetchColumn();
        return $count > 0;
    }

    public function create($nombre)
    {
        $uuid = uuid4();
        $stmt = $this->pdo->prepare("INSERT INTO categorias (id, nombre) VALUES (:id, :nombre)");
        $stmt->execute(['id' => $uuid, 'nombre' => $nombre]);
    }

    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categorias WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return false;
        }
        $categoria = new Categoria(
            $row['id'],
            $row['nombre'],
            $row['created_at'],
            $row['updated_at'],
            $row['is_deleted']
        );
        return $categoria;
    }

    public function update($id, string $nombre)
    {
        $stmt = $this->pdo->prepare("UPDATE categorias SET nombre = :nombre WHERE id = :id");
        $stmt->execute(['id' => $id, 'nombre' => $nombre]);
    }


}