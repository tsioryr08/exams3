<?php
class CategoryRepository {
  private $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  /**
   * Retourne la liste des catégories avec le nombre d'objets par catégorie.
   * @return array
   */
  public function getAll(): array
  {
    $sql = "SELECT c.id, c.nom, c.description, COUNT(o.id) AS objets
            FROM categorie c
            LEFT JOIN objet o ON o.categorie_id = c.id
            GROUP BY c.id
            ORDER BY c.id";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Récupère une catégorie par son ID.
   * @return array|null
   */
  public function getById(int $id): ?array
  {
    $sql = "SELECT id, nom, description FROM categorie WHERE id = :id LIMIT 1";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
  }

  /**
   * Crée une catégorie et retourne son id.
   * @return int nouvel id
   */
  public function create(string $nom, string $description): int
  {
    $sql = 'INSERT INTO categorie (nom, description) VALUES (:nom, :description)';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
    $stmt->bindValue(':description', $description, PDO::PARAM_STR);
    $stmt->execute();
    return (int)$this->pdo->lastInsertId();
  }

  /**
   * Met à jour le nom et la description d'une catégorie.
   * @return bool
   */
  public function update(int $id, string $nom, string $description): bool
  {
    $sql = 'UPDATE categorie SET nom = :nom, description = :description WHERE id = :id';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
    $stmt->bindValue(':description', $description, PDO::PARAM_STR);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
  }

  /**
   * Supprime une catégorie par id.
   * @return bool
   */
  public function delete(int $id): bool
  {
    $sql = 'DELETE FROM categorie WHERE id = :id';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
  }
}
