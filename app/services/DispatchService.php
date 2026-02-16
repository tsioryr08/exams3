<?php

require_once __DIR__ . '/../repositories/DispatchRepository.php';
require_once __DIR__ . '/../repositories/BesoinRepository.php';
require_once __DIR__ . '/../repositories/DonRepository.php';

class DispatchService
{
    private $dispatchRepository;
    private $besoinRepository;
    private $donRepository;

    public function __construct($pdo)
    {
        $this->dispatchRepository = new DispatchRepository($pdo);
        $this->besoinRepository = new BesoinRepository($pdo);
        $this->donRepository = new DonRepository($pdo);
    }

    /**
     * Lancer le dispatch automatique des dons
     * @return array - Résumé du dispatch
     */
    public function runDispatch()
    {
        $rapport = [
            'success' => true,
            'message' => '',
            'dons_traites' => 0,
            'attributions' => 0,
            'quantite_totale_distribuee' => 0,
            'details' => []
        ];

        try {
            // 1. Vider les dispatches existants (réinitialiser)
            $this->dispatchRepository->deleteAll();

            // 2. Récupérer tous les dons triés par date ASC (premier arrivé = premier servi)
            $dons = $this->donRepository->getAllOrderByDateAsc();

            if (empty($dons)) {
                $rapport['message'] = 'Aucun don à dispatcher';
                return $rapport;
            }

            // 3. Pour chaque don, distribuer aux besoins correspondants
            foreach ($dons as $don) {
                $donId = $don['id'];
                $type = $don['type'];
                $libelle = $don['libelle'];
                $quantiteDon = $don['quantite'];
                $quantiteRestante = $quantiteDon;

                $rapport['dons_traites']++;

                // Récupérer les besoins correspondants (même type ET même libellé)
                $besoins = $this->besoinRepository->getByTypeAndLibelle($type, $libelle);

                if (empty($besoins)) {
                    // Pas de besoin correspondant pour ce don
                    $rapport['details'][] = [
                        'don_id' => $donId,
                        'libelle' => $libelle,
                        'quantite' => $quantiteDon,
                        'statut' => 'Non attribué (aucun besoin correspondant)'
                    ];
                    continue;
                }

                // Distribuer la quantité du don aux besoins dans l'ordre
                foreach ($besoins as $besoin) {
                    if ($quantiteRestante <= 0) {
                        break; // Don épuisé
                    }

                    $besoinId = $besoin['id'];
                    $villeId = $besoin['ville_id'];
                    $villeName = $besoin['ville_nom'];
                    $quantiteBesoin = $besoin['quantite'];

                    // Calculer combien a déjà été dispatché pour ce besoin
                    $dejaDispatche = $this->dispatchRepository->getTotalDispatchedForBesoin(
                        $villeId, 
                        $type, 
                        $libelle
                    );

                    // Calculer le besoin restant
                    $besoinRestant = $quantiteBesoin - $dejaDispatche;

                    if ($besoinRestant <= 0) {
                        // Ce besoin est déjà satisfait, passer au suivant
                        continue;
                    }

                    // Déterminer combien on peut attribuer
                    $quantiteAAttribuer = min($quantiteRestante, $besoinRestant);

                    // Créer le dispatch
                    $this->dispatchRepository->create(
                        $donId,
                        $villeId,
                        $libelle,
                        $quantiteAAttribuer
                    );

                    // Mettre à jour les compteurs
                    $quantiteRestante -= $quantiteAAttribuer;
                    $rapport['attributions']++;
                    $rapport['quantite_totale_distribuee'] += $quantiteAAttribuer;

                    // Ajouter au rapport détaillé
                    $rapport['details'][] = [
                        'don_id' => $donId,
                        'ville' => $villeName,
                        'libelle' => $libelle,
                        'quantite_attribuee' => $quantiteAAttribuer,
                        'statut' => 'Attribué'
                    ];
                }

                // Si le don n'est pas totalement distribué
                if ($quantiteRestante > 0) {
                    $rapport['details'][] = [
                        'don_id' => $donId,
                        'libelle' => $libelle,
                        'quantite' => $quantiteRestante,
                        'statut' => 'Partiellement attribué (reste: ' . $quantiteRestante . ')'
                    ];
                }
            }

            $rapport['message'] = sprintf(
                'Dispatch terminé ! %d don(s) traité(s), %d attribution(s) créée(s), %d unité(s) distribuée(s)',
                $rapport['dons_traites'],
                $rapport['attributions'],
                $rapport['quantite_totale_distribuee']
            );

        } catch (Exception $e) {
            $rapport['success'] = false;
            $rapport['message'] = 'Erreur lors du dispatch: ' . $e->getMessage();
            error_log("Erreur dispatch: " . $e->getMessage());
        }

        return $rapport;
    }

    /**
     * Récupérer le résumé du dispatch par ville
     * @return array
     */
    public function getDispatchSummary()
    {
        return $this->dispatchRepository->getAll();
    }
}
