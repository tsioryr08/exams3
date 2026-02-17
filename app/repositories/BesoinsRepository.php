<?php
class BesoinsRepository {
  private $pdo;
  public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    
  public function createBesoin($ville_id, $type, $libelle, $prix_unitaire, $quantite, $date_saisie) {
    $st = $this->pdo->prepare("
      INSERT INTO besoins(ville_id, type, libelle, prix_unitaire, quantite, date_saisie)
      VALUES(?,?,?,?,?,?)
    ");
    $st->execute([(int)$ville_id, (string)$type, (string)$libelle, (float)$prix_unitaire, (int)$quantite, (string)$date_saisie]);
    return $this->pdo->lastInsertId();
  }

  public function getAllVilles() {
    $st = $this->pdo->prepare("SELECT id, nom, region FROM villes ORDER BY nom");
    $st->execute();
    return $st->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getAllBesoins() {
    $st = $this->pdo->prepare("
      SELECT b.id, v.nom as ville, b.type, b.libelle, b.prix_unitaire, b.quantite, b.date_saisie 
      FROM besoins b
      JOIN villes v ON b.ville_id = v.id
      ORDER BY b.date_saisie DESC
    ");
    $st->execute();
    return $st->fetchAll(PDO::FETCH_ASSOC);
  }
}
