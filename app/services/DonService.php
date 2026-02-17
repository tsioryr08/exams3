<?php

require_once __DIR__ . '/../repositories/DonRepository.php';

class DonService
{
    private $donRepository;

    public function __construct($pdo)
    {
        $this->donRepository = new DonRepository($pdo);
    }

    /**
     * Ajouter un nouveau don
     * @param string $type - Type du don
     * @param string $libelle - Libellé 
     * @param int $quantite - Quantité
     * @return array - ['success' => bool, 'message' => string]
     */
    public function add($type, $libelle, $quantite)
    {
        // Validation
        $errors = $this->validate($type, $libelle, $quantite);
        
        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => implode('<br>', $errors)
            ];
        }

        // Création du don
        $result = $this->donRepository->create($type, $libelle, $quantite);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Don enregistré avec succès'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement du don'
            ];
        }
    }

    /**
     * Récupérer la liste des dons
     * @return array - Liste des dons
     */
    public function list()
    {
        return $this->donRepository->getAllOrderByDate();
    }

    /**
     * Valider les données du don
     * @param string $type
     * @param string $libelle
     * @param int $quantite
     * @return array - Tableau des erreurs
     */
    private function validate($type, $libelle, $quantite)
    {
        $errors = [];

        // Validation du type
        $typesValides = ['nature', 'materiel', 'argent'];
        if (empty($type)) {
            $errors[] = 'Le type est obligatoire';
        } elseif (!in_array($type, $typesValides)) {
            $errors[] = 'Type invalide. Choisissez parmi: nature, matériel, argent';
        }

        // Validation du libellé
        if (empty($libelle)) {
            $errors[] = 'Le libellé est obligatoire';
        } elseif (strlen($libelle) > 100) {
            $errors[] = 'Le libellé ne doit pas dépasser 100 caractères';
        }

        // Validation de la quantité
        if (empty($quantite)) {
            $errors[] = 'La quantité est obligatoire';
        } elseif (!is_numeric($quantite) || $quantite <= 0) {
            $errors[] = 'La quantité doit être un nombre positif';
        }

        return $errors;
    }
}
