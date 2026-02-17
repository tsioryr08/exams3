<?php

require_once __DIR__ . '/../repositories/AchatRepository.php';
require_once __DIR__ . '/../repositories/BesoinRepository.php';
require_once __DIR__ . '/../repositories/ConfigRepository.php';
require_once __DIR__ . '/../repositories/DonRepository.php';
require_once __DIR__ . '/../repositories/DispatchRepository.php';

/**
 * Service gérant la logique métier des achats
 */
class AchatService
{
    private $achatRepo;
    private $besoinRepo;
    private $configRepo;
    private $donRepo;
    private $dispatchRepo;
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->achatRepo = new AchatRepository($pdo);
        $this->besoinRepo = new BesoinRepository($pdo);
        $this->configRepo = new ConfigRepository($pdo);
        $this->donRepo = new DonRepository($pdo);
        $this->dispatchRepo = new DispatchRepository($pdo);
    }

    /**
     * Récupérer les besoins restants agrégés (sans distinction de ville) avec montant d'argent disponible
     * @return array
     */
    public function getBesoinsRestantsAvecArgent()
    {
        $besoins = $this->besoinRepo->getTotalBesoinsRestantsAgreges();
        $donsDisponibles = $this->donRepo->getDonsDisponiblesParTypeLibelle();
        $argentDisponible = $this->getArgentDisponible();
        $fraisPourcentage = $this->configRepo->getFraisAchatPourcentage();

        // Calculer le montant avec frais pour chaque besoin et vérifier les dons disponibles
        foreach ($besoins as &$besoin) {
            $key = $besoin['type'] . '_' . $besoin['libelle'];
            $donDisponible = $donsDisponibles[$key]['quantite_disponible'] ?? 0;
            
            // Quantité qu'il faut vraiment acheter = besoin restant - don disponible
            $quantiteAacheter = max(0, $besoin['quantite_restante'] - $donDisponible);
            
            $besoin['don_disponible'] = $donDisponible;
            $besoin['quantite_a_acheter'] = $quantiteAacheter;
            
            $montantSansFrais = $quantiteAacheter * $besoin['prix_unitaire'];
            $frais = $montantSansFrais * ($fraisPourcentage / 100);
            $besoin['frais_achat'] = $frais;
            $besoin['montant_avec_frais'] = $montantSansFrais + $frais;
            $besoin['pourcentage_frais'] = $fraisPourcentage;
            
            // Peut acheter si : quantité à acheter > 0 ET argent suffisant
            $besoin['peut_acheter'] = ($quantiteAacheter > 0) && ($besoin['montant_avec_frais'] <= $argentDisponible);
            $besoin['besoin_achat'] = $quantiteAacheter > 0; // Si on a besoin d'acheter (pas juste couvert par dons)
        }

        return [
            'besoins' => $besoins,
            'argent_disponible' => $argentDisponible,
            'frais_pourcentage' => $fraisPourcentage
        ];
    }

    /**
     * Calculer l'argent disponible (dons argent - achats validés)
     * @return float
     */
    public function getArgentDisponible()
    {
        try {
            // Total des dons en argent
            $dons = $this->donRepo->getAll();
            $totalDonsArgent = 0;
            foreach ($dons as $don) {
                if ($don['type'] === 'argent') {
                    $totalDonsArgent += $don['quantite'];
                }
            }

            // Total des achats validés
            $totalAchats = $this->achatRepo->getTotalMontantUtilise();

            return $totalDonsArgent - $totalAchats;
        } catch (Exception $e) {
            error_log("Erreur calcul argent disponible: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Simuler des achats (sans valider) - Nouvelle version agrégée sans villes
     * @param array $besoinKeys - Liste des clés type_libelle des besoins à acheter
     * @return array
     */
    public function simulerAchats($besoinKeys)
    {
        $fraisPourcentage = $this->configRepo->getFraisAchatPourcentage();
        $argentDisponible = $this->getArgentDisponible();
        $besoinsAgreges = $this->besoinRepo->getTotalBesoinsRestantsAgreges();
        $donsDisponibles = $this->donRepo->getDonsDisponiblesParTypeLibelle();
        
        $simulation = [
            'success' => true,
            'errors' => [],
            'achats' => [],
            'total_sans_frais' => 0,
            'total_frais' => 0,
            'total_avec_frais' => 0,
            'argent_disponible' => $argentDisponible,
            'argent_restant' => $argentDisponible
        ];

        if (empty($besoinKeys)) {
            $simulation['success'] = false;
            $simulation['errors'][] = 'Aucun besoin sélectionné pour l\'achat';
            return $simulation;
        }

        foreach ($besoinKeys as $besoinKey) {
            // Trouver le besoin agrégé correspondant
            $besoinAgrege = null;
            foreach ($besoinsAgreges as $ba) {
                $key = $ba['type'] . '_' . $ba['libelle'];
                if ($key === $besoinKey) {
                    $besoinAgrege = $ba;
                    break;
                }
            }
            
            if (!$besoinAgrege) {
                $simulation['errors'][] = "Besoin '$besoinKey' introuvable";
                continue;
            }

            // Calculer la quantité à acheter (besoin - dons disponibles)
            $donDisponible = $donsDisponibles[$besoinKey]['quantite_disponible'] ?? 0;
            $quantiteAacheter = max(0, $besoinAgrege['quantite_restante'] - $donDisponible);
            
            if ($quantiteAacheter <= 0) {
                $simulation['errors'][] = "Le besoin '{$besoinAgrege['libelle']}' est déjà couvert par les dons disponibles";
                continue;
            }

            // Calculer les montants
            $prixUnitaire = $besoinAgrege['prix_unitaire'];
            $montantSansFrais = $quantiteAacheter * $prixUnitaire;
            $frais = $montantSansFrais * ($fraisPourcentage / 100);
            $montantAvecFrais = $montantSansFrais + $frais;

            // Vérifier si on a assez d'argent
            if ($simulation['argent_restant'] < $montantAvecFrais) {
                $simulation['errors'][] = "Argent insuffisant pour acheter '{$besoinAgrege['libelle']}' (besoin: " . number_format($montantAvecFrais, 2) . " Ar, disponible: " . number_format($simulation['argent_restant'], 2) . " Ar)";
                $simulation['success'] = false;
                continue;
            }

            // Ajouter à la simulation
            $simulation['achats'][] = [
                'type' => $besoinAgrege['type'],
                'libelle' => $besoinAgrege['libelle'],
                'quantite' => $quantiteAacheter,
                'prix_unitaire' => $prixUnitaire,
                'montant_sans_frais' => $montantSansFrais,
                'frais' => $frais,
                'montant_avec_frais' => $montantAvecFrais,
                'pourcentage_frais' => $fraisPourcentage,
                'besoin_key' => $besoinKey
            ];

            $simulation['total_sans_frais'] += $montantSansFrais;
            $simulation['total_frais'] += $frais;
            $simulation['total_avec_frais'] += $montantAvecFrais;
            $simulation['argent_restant'] -= $montantAvecFrais;
        }

        return $simulation;
    }

    /**
     * Valider les achats et les créer comme des dons (pas encore dispatchés aux villes)
     * @param array $besoinKeys - Liste des clés type_libelle des besoins à acheter
     * @return array
     */
    public function validerAchats($besoinKeys)
    {
        // D'abord simuler pour validation
        $simulation = $this->simulerAchats($besoinKeys);

        if (!$simulation['success'] || !empty($simulation['errors'])) {
            return [
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $simulation['errors']
            ];
        }

        // Transaction pour garantir la cohérence
        try {
            $this->pdo->beginTransaction();

            $donsCreés = [];
            
            foreach ($simulation['achats'] as $achat) {
                // Créer un don avec les quantités achetées
                // Ces dons seront dispatchés plus tard aux villes via le système de dispatch
                $success = $this->donRepo->create(
                    $achat['type'],
                    $achat['libelle'],
                    $achat['quantite']
                );

                if ($success) {
                    $donsCreés[] = [
                        'type' => $achat['type'],
                        'libelle' => $achat['libelle'],
                        'quantite' => $achat['quantite']
                    ];
                }
            }

            // Enregistrer l'achat dans la table achats pour historique (optionnel)
            // Pour l'instant on stocke juste les dons

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => count($donsCreés) . ' achat(s) validé(s) et ajouté(s) aux dons disponibles',
                'dons_crees' => $donsCreés,
                'total_depense' => $simulation['total_avec_frais'],
                'argent_restant' => $simulation['argent_restant']
            ];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Erreur validation achats: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de la validation: ' . $e->getMessage(),
                'errors' => [$e->getMessage()]
            ];
        }
    }

}
