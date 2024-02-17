<?php

namespace services;

use models\Funko;
use PDO;
use Ramsey\Uuid\Uuid;


require_once __DIR__ . '/../models/Funko.php';

class FunkosService
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAllWithCategoryName($searchTerm = null)
    {
        $sql = "SELECT f.*, c.nombre AS categoria_nombre
        FROM funkos f
        LEFT JOIN categorias c ON f.categoria_id = c.id";


        if ($searchTerm) {
            $searchTerm = '%' . strtolower($searchTerm) . '%'; // Convertir el término de búsqueda a minúsculas
            $sql .= " WHERE LOWER(f.marca) LIKE :searchTerm OR LOWER(f.modelo) LIKE :searchTerm";
        }

        $sql .= " ORDER BY f.id ASC";

        $stmt = $this->pdo->prepare($sql);

        if ($searchTerm) {
            // Vincula el mismo término de búsqueda a los dos parámetros de búsqueda
            $stmt->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);
        }

        $stmt->execute();

        $funkos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $funko = new Funko(
                $row['id'],
                $row['uuid'],
                $row['descripcion'],
                $row['imagen'],
                $row['marca'],
                $row['modelo'],
                $row['precio'],
                $row['stock'],
                $row['created_at'],
                $row['updated_at'],
                $row['categoria_id'],
                $row['categoria_nombre'], // Pasamos el nombre de la categoría
                $row['is_deleted']
            );
            $funkos[] = $funko;
        }
        return $funkos;
    }

    public function findById($id)
    {
        $sql = "SELECT f.*, c.nombre AS categoria_nombre
            FROM funkos f
            LEFT JOIN categorias c ON f.categoria_id = c.id
            WHERE f.id = :id"; // Filtrar por ID

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT); // Vincular el ID como un entero
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null; // Si no se encuentra el funko, devolver null
        }

        // Crear y devolver un objeto funko con los datos obtenidos
        $funko = new funko(
            $row['id'],
            $row['uuid'],
            $row['descripcion'],
            $row['imagen'],
            $row['marca'],
            $row['modelo'],
            $row['precio'],
            $row['stock'],
            $row['created_at'],
            $row['updated_at'],
            $row['categoria_id'],
            $row['categoria_nombre'], // Pasamos el nombre de la categoría
            $row['is_deleted']
        );

        return $funko;
    }

    public function deleteById($id)
    {
        $sql = "DELETE FROM funkos WHERE id = :id"; // Consulta SQL para eliminar

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT); // Vincular el ID como un entero

        return $stmt->execute(); // Ejecutar la consulta y devolver el resultado
    }

    public function update(funko $funko)
    {
        $sql = "UPDATE funkos SET
            descripcion = :descripcion,
            imagen = :imagen,
            marca = :marca,
            modelo = :modelo,
            precio = :precio,
            stock = :stock,
            categoria_id = :categoria_id,
            updated_at = :updated_at
            WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':descripcion', $funko->descripcion, PDO::PARAM_STR);
        $stmt->bindValue(':imagen', $funko->imagen, PDO::PARAM_STR);
        $stmt->bindValue(':marca', $funko->marca, PDO::PARAM_STR);
        $stmt->bindValue(':modelo', $funko->modelo, PDO::PARAM_STR);
        $stmt->bindValue(':precio', $funko->precio, PDO::PARAM_STR);
        $stmt->bindValue(':stock', $funko->stock, PDO::PARAM_INT);
        $stmt->bindValue(':categoria_id', $funko->categoriaId, PDO::PARAM_INT);
        $funko->updatedAt = date('Y-m-d H:i:s');
        $stmt->bindValue(':updated_at', $funko->updatedAt, PDO::PARAM_STR);
        $stmt->bindValue(':id', $funko->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function save(funko $funko)
    {
        $sql = "INSERT INTO funkos (uuid, descripcion, imagen, marca, modelo, precio, stock, categoria_id, created_at, updated_at)
            VALUES (:uuid, :descripcion, :imagen, :marca, :modelo, :precio, :stock, :categoria_id, :created_at, :updated_at)";

        $stmt = $this->pdo->prepare($sql);

        $funko->uuid = Uuid::uuid4()->toString(); //uniqid(); // Generar un ID único
        $stmt->bindValue(':uuid', $funko->uuid, PDO::PARAM_STR);
        $stmt->bindValue(':descripcion', $funko->descripcion, PDO::PARAM_STR);
        $funko->imagen = funko::$IMAGEN_DEFAULT;
        $stmt->bindValue(':imagen', $funko->imagen, PDO::PARAM_STR);
        $stmt->bindValue(':marca', $funko->marca, PDO::PARAM_STR);
        $stmt->bindValue(':modelo', $funko->modelo, PDO::PARAM_STR);
        $stmt->bindValue(':precio', $funko->precio, PDO::PARAM_STR);
        $stmt->bindValue(':stock', $funko->stock, PDO::PARAM_INT);
        $stmt->bindValue(':categoria_id', $funko->categoriaId, PDO::PARAM_INT);
        $funko->createdAt = date('Y-m-d H:i:s');
        $stmt->bindValue(':created_at', $funko->createdAt, PDO::PARAM_STR);
        $funko->updatedAt = date('Y-m-d H:i:s');
        $stmt->bindValue(':updated_at', $funko->updatedAt, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
