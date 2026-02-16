<?php
class BesoinsService {
  private $repo;
  public function __construct(BesoinsRepository $repo) { $this->repo = $repo; }

  public function validateAndCreate($data) {
    $errors = [];

    // Validation ville_id
    if (empty($data['ville_id'])) {
      $errors['ville_id'] = 'Veuillez sélectionner une ville.';
    }

    // Validation type
    if (empty($data['type']) || !in_array($data['type'], ['nature', 'materiel', 'argent'])) {
      $errors['type'] = 'Veuillez sélectionner un type valide.';
    }

    // Validation libellé
    if (empty($data['libelle']) || strlen($data['libelle']) < 3) {
      $errors['libelle'] = 'Le libellé doit contenir au moins 3 caractères.';
    }

    // Validation prix unitaire
    if (!isset($data['prix_unitaire']) || $data['prix_unitaire'] <= 0) {
      $errors['prix_unitaire'] = 'Le prix unitaire doit être supérieur à 0.';
    }

    // Validation quantité
    if (!isset($data['quantite']) || $data['quantite'] <= 0) {
      $errors['quantite'] = 'La quantité doit être supérieure à 0.';
    }

    // Validation date
    if (empty($data['date_saisie'])) {
      $errors['date_saisie'] = 'Veuillez saisir une date.';
    }

    if (!empty($errors)) {
      return ['success' => false, 'errors' => $errors];
    }

    // Créer le besoin
    $id = $this->repo->createBesoin(
      $data['ville_id'],
      $data['type'],
      $data['libelle'],
      $data['prix_unitaire'],
      $data['quantite'],
      $data['date_saisie']
    );

    return ['success' => true, 'id' => $id];
  }

  public function getAllVilles() {
    return $this->repo->getAllVilles();
  }

  public function getAllBesoins() {
    return $this->repo->getAllBesoins();
  }
}
