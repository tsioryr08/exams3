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
     * Récupérer les besoins restants avec montant d'argent disponible
     * @param int|null $villeId
     * @return array
     */
    public function getBesoinsRestantsAvecArgent($villeId = null)
    {
        $besoins = $this->besoinRepo->getBesoinsRestants($villeId);
        $argentDisponible = $this->getArgentDisponible();
        $fraisPourcentage = $this->configRepo->getFraisAchatPourcentage();

        // Calculer le montant avec frais pour chaque besoin
        foreach ($besoins as &$besoin) {
            $montantSansFrais = $besoin['montant_restant'];
            $frais = $montantSansFrais * ($fraisPourcentage / 100);
            $besoin['frais_achat'] = $frais;
            $besoin['montant_avec_frais'] = $montantSansFrais + $frais;
            $besoin['pourcentage_frais'] = $fraisPourcentage;
            $besoin['peut_acheter'] = $besoin['montant_avec_frais'] <= $argentDisponible;
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
     * Simuler des achats (sans valider)
     * @param array $besoinIds - Liste des IDs de besoins à acheter
     * @return array
     */
    public function simulerAchats($besoinIds)
    {
        $fraisPourcentage = $this->configRepo->getFraisAchatPourcentage();
        $argentDisponible = $this->getArgentDisponible();
        
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

        if (empty($besoinIds)) {
            $simulation['success'] = false;
            $simulation['errors'][] = 'Aucun besoin sélectionné pour l\'achat';
            return $simulation;
        }

        foreach ($besoinIds as $besoinId) {
            $besoin = $this->besoinRepo->getById($besoinId);
            
            if (!$besoin) {
                $simulation['errors'][] = "Besoin #$besoinId introuvable";
                continue;
            }

            // Vérifier que c'est un besoin achetable
            if (!in_array($besoin['type'], ['nature', 'materiel'])) {
                $simulation['errors'][] = "Le besoin '{$besoin['libelle']}' n'est pas achetable (type: {$besoin['type']})";
                continue;
            }

            // Calculer la quantité restante
            $besoinsRestants = $this->besoinRepo->getBesoinsRestants();
            $besoinRestant = null;
            foreach ($besoinsRestants as $br) {
                if ($br['besoin_id'] == $besoinId) {
                    $besoinRestant = $br;
                    break;
                }
            }

            if (!$besoinRestant || $besoinRestant['quantite_restante'] <= 0) {
                $simulation['errors'][] = "Le besoin '{$besoin['libelle']}' est déjà satisfait";
                continue;
            }

            // Vérifier si un don direct couvre déjà ce besoin
            $donDirectExiste = $this->verifierDonDirectExistant($besoin['type'], $besoin['libelle']);
            if ($donDirectExiste) {
                $simulation['errors'][] = "Un don direct existe déjà pour '{$besoin['libelle']}' - l'achat n'est pas nécessaire";
                continue;
            }

            // Calculer les montants
            $quantite = $besoinRestant['quantite_restante'];
            $prixUnitaire = $besoin['prix_unitaire'];
            $montantSansFrais = $quantite * $prixUnitaire;
            $frais = $montantSansFrais * ($fraisPourcentage / 100);
            $montantAvecFrais = $montantSansFrais + $frais;

            // Vérifier si on a assez d'argent
            if ($simulation['argent_restant'] < $montantAvecFrais) {
                $simulation['errors'][] = "Argent insuffisant pour acheter '{$besoin['libelle']}' (besoin: " . number_format($montantAvecFrais, 2) . " Ar, disponible: " . number_format($simulation['argent_restant'], 2) . " Ar)";
                $simulation['success'] = false;
                continue;
            }

            // Ajouter à la simulation
            $simulation['achats'][] = [
                'besoin_id' => $besoinId,
                'ville_id' => $besoin['ville_id'],
                'ville_nom' => $besoin['ville_nom'],
                'type' => $besoin['type'],
                'libelle' => $besoin['libelle'],
                'quantite' => $quantite,
                'prix_unitaire' => $prixUnitaire,
                'montant_sans_frais' => $montantSansFrais,
                'frais' => $frais,
                'montant_avec_frais' => $montantAvecFrais,
                'pourcentage_frais' => $fraisPourcentage
            ];

            $simulation['total_sans_frais'] += $montantSansFrais;
            $simulation['total_frais'] += $frais;
            $simulation['total_avec_frais'] += $montantAvecFrais;
            $simulation['argent_restant'] -= $montantAvecFrais;
        }

        return $simulation;
    }

    /**
     * Valider les achats et créer les dispatches
     * @param array $besoinIds
     * @return array
     */
    public function validerAchats($besoinIds)
    {
        // D'abord simuler pour validation
        $simulation = $this->simulerAchats($besoinIds);

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

            $achatsCreés = [];
            
            foreach ($simulation['achats'] as $achat) {
                // Créer l'achat
                $achatId = $this->achatRepo->create([
                    'ville_id' => $achat['ville_id'],
                    'besoin_id' => $achat['besoin_id'],
                    'type' => $achat['type'],
                    'libelle' => $achat['libelle'],
                    'quantite' => $achat['quantite'],
                    'prix_unitaire' => $achat['prix_unitaire'],
                    'montant_total' => $achat['montant_sans_frais'],
                    'frais_achat' => $achat['frais'],
                    'montant_final' => $achat['montant_avec_frais'],
                    'pourcentage_frais' => $achat['pourcentage_frais'],
                    'statut' => 'valide'
                ]);

                // Créer le dispatch correspondant
                $this->dispatchRepo->createFromAchat(
                    $achatId,
                    $achat['ville_id'],
                    $achat['libelle'],
                    $achat['quantite']
                );

                $achatsCreés[] = $achatId;
            }

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => count($achatsCreés) . ' achat(s) validé(s) avec succès',
                'achats_ids' => $achatsCreés,
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

    /**
     * Vérifier si un don direct existe pour ce type et libellé
     * @param string $type
     * @param string $libelle
     * @return bool
     */
    private function verifierDonDirectExistant($type, $libelle)
    {
        $dons = $this->donRepo->getAll();
        foreach ($dons as $don) {
            if ($don['type'] === $type && $don['libelle'] === $libelle && $don['quantite'] > 0) {
                return true;
            }
        }
        return false;
    }
}
