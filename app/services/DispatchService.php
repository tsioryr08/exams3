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

    /**
     * MÉTHODE 1: Dispatch par date (FIFO - First In First Out)
     * Les besoins créés en premier sont servis en premier
     */
    public function dispatchParDate()
    {
        // Réinitialiser les dispatches
        $this->reinitialiserDispatches();

        $resultats = [
            'attributions' => [],
            'restes' => [],
            'stats' => [
                'total_dons' => 0,
                'dons_attribues' => 0,
                'quantite_distribuee' => 0,
                'quantite_reste' => 0
            ],
            'par_ville' => []
        ];

        try {
            // Récupérer tous les dons
            $dons = $this->donRepository->getAllOrderByDateAsc();
            $resultats['stats']['total_dons'] = count($dons);

            foreach ($dons as $don) {
                $quantiteRestante = $don['quantite'];
                
                // Récupérer les besoins correspondants triés par date de création
                $besoins = $this->besoinRepository->getByTypeAndLibelleOrderByDate($don['type'], $don['libelle']);

                foreach ($besoins as $besoin) {
                    if ($quantiteRestante <= 0) break;

                    // Vérifier combien a déjà été dispatché pour ce besoin
                    $dejaDispatche = $this->dispatchRepository->getTotalDispatchedForBesoin(
                        $besoin['ville_id'], 
                        $don['type'], 
                        $don['libelle']
                    );

                    $besoinRestant = $besoin['quantite'] - $dejaDispatche;

                    if ($besoinRestant > 0) {
                        $quantiteAAttribuer = min($quantiteRestante, $besoinRestant);
                        
                        // Créer le dispatch
                        $this->dispatchRepository->create(
                            $don['id'],
                            $besoin['ville_id'],
                            $don['libelle'],
                            $quantiteAAttribuer
                        );

                        $resultats['attributions'][] = [
                            'don_id' => $don['id'],
                            'type' => $don['type'],
                            'libelle' => $don['libelle'],
                            'ville_nom' => $besoin['ville_nom'],
                            'quantite_attribuee' => $quantiteAAttribuer,
                            'date_saisie' => $besoin['date_saisie'] // Pour le tri
                        ];

                        $quantiteRestante -= $quantiteAAttribuer;
                        $resultats['stats']['quantite_distribuee'] += $quantiteAAttribuer;
                    }
                }

                // S'il reste des quantités non distribuées
                if ($quantiteRestante > 0) {
                    $resultats['restes'][] = [
                        'type' => $don['type'],
                        'libelle' => $don['libelle'],
                        'quantite' => $quantiteRestante
                    ];
                    $resultats['stats']['quantite_reste'] += $quantiteRestante;
                } else {
                    $resultats['stats']['dons_attribues']++;
                }
            }

            // Trier les attributions par date de saisie (ordre chronologique)
            usort($resultats['attributions'], function($a, $b) {
                return strtotime($a['date_saisie']) - strtotime($b['date_saisie']);
            });
            
            $resultats['par_ville'] = $this->getStatistiquesParVilleAvecDate();
            usort($resultats['par_ville'], function($a, $b) {
                return strtotime($a['premiere_date']) - strtotime($b['premiere_date']);
            });

        } catch (Exception $e) {
            error_log("Erreur dispatch par date: " . $e->getMessage());
        }

        return $resultats;
    }

    /**
     * MÉTHODE 2: Dispatch par ordre croissant
     * Priorité aux villes avec les demandes les plus faibles
     */
    public function dispatchOrdreCroissant()
    {
        // Réinitialiser les dispatches
        $this->reinitialiserDispatches();

        $resultats = [
            'attributions' => [],
            'restes' => [],
            'stats' => [
                'total_dons' => 0,
                'dons_attribues' => 0,
                'quantite_distribuee' => 0,
                'quantite_reste' => 0
            ],
            'par_ville' => []
        ];

        try {
            // Récupérer tous les dons
            $dons = $this->donRepository->getAllOrderByDateAsc();
            $resultats['stats']['total_dons'] = count($dons);

            foreach ($dons as $don) {
                $quantiteRestante = $don['quantite'];
                
                // Récupérer les besoins correspondants triés par quantité croissante
                $besoins = $this->besoinRepository->getByTypeAndLibelleOrderByQuantiteAsc($don['type'], $don['libelle']);

                foreach ($besoins as $besoin) {
                    if ($quantiteRestante <= 0) break;

                    // Vérifier combien a déjà été dispatché pour ce besoin
                    $dejaDispatche = $this->dispatchRepository->getTotalDispatchedForBesoin(
                        $besoin['ville_id'], 
                        $don['type'], 
                        $don['libelle']
                    );

                    $besoinRestant = $besoin['quantite'] - $dejaDispatche;

                    if ($besoinRestant > 0) {
                        // On essaie de satisfaire complètement le besoin si possible
                        $quantiteAAttribuer = min($quantiteRestante, $besoinRestant);
                        
                        // Créer le dispatch
                        $this->dispatchRepository->create(
                            $don['id'],
                            $besoin['ville_id'],
                            $don['libelle'],
                            $quantiteAAttribuer
                        );

                        $resultats['attributions'][] = [
                            'don_id' => $don['id'],
                            'type' => $don['type'],
                            'libelle' => $don['libelle'],
                            'ville_nom' => $besoin['ville_nom'],
                            'quantite_attribuee' => $quantiteAAttribuer,
                            'quantite_demandee' => $besoin['quantite'] // Pour le tri
                        ];

                        $quantiteRestante -= $quantiteAAttribuer;
                        $resultats['stats']['quantite_distribuee'] += $quantiteAAttribuer;
                    }
                }

                // S'il reste des quantités non distribuées
                if ($quantiteRestante > 0) {
                    $resultats['restes'][] = [
                        'type' => $don['type'],
                        'libelle' => $don['libelle'],
                        'quantite' => $quantiteRestante
                    ];
                    $resultats['stats']['quantite_reste'] += $quantiteRestante;
                } else {
                    $resultats['stats']['dons_attribues']++;
                }
            }

            // Trier les attributions par quantité demandée croissante (petites en premier)
            usort($resultats['attributions'], function($a, $b) {
                return $a['quantite_demandee'] - $b['quantite_demandee'];
            });

            // Calculer les stats par ville et les trier par quantité demandée croissante
            $resultats['par_ville'] = $this->getStatistiquesParVille();
            usort($resultats['par_ville'], function($a, $b) {
                return $a['demandee'] - $b['demandee'];
            });

        } catch (Exception $e) {
            error_log("Erreur dispatch ordre croissant: " . $e->getMessage());
        }

        return $resultats;
    }

    /**
     * MÉTHODE 3: Dispatch proportionnel
     * Distribution proportionnelle : (demande_ville / total_demandes) * quantité_disponible
     * On prend toujours la partie entière inférieure (floor)
     */
    public function dispatchProportionnel()
    {
        // Réinitialiser les dispatches
        $this->reinitialiserDispatches();

        $resultats = [
            'attributions' => [],
            'restes' => [],
            'stats' => [
                'total_dons' => 0,
                'dons_attribues' => 0,
                'quantite_distribuee' => 0,
                'quantite_reste' => 0
            ],
            'par_ville' => []
        ];

        try {
            // Récupérer tous les dons
            $dons = $this->donRepository->getAllOrderByDateAsc();
            $resultats['stats']['total_dons'] = count($dons);

            foreach ($dons as $don) {
                $quantiteDisponible = $don['quantite'];
                
                // Récupérer les besoins correspondants
                $besoins = $this->besoinRepository->getByTypeAndLibelle($don['type'], $don['libelle']);

                if (empty($besoins)) {
                    // Aucun besoin correspondant
                    $resultats['restes'][] = [
                        'type' => $don['type'],
                        'libelle' => $don['libelle'],
                        'quantite' => $quantiteDisponible
                    ];
                    $resultats['stats']['quantite_reste'] += $quantiteDisponible;
                    continue;
                }

                // Calculer le total des demandes
                $totalDemandes = 0;
                foreach ($besoins as $besoin) {
                    $totalDemandes += $besoin['quantite'];
                }

                if ($totalDemandes == 0) continue;

                $quantiteDistribuee = 0;

                // Calculer et attribuer proportionnellement
                foreach ($besoins as $besoin) {
                    // Formule : (demande_ville / total_demandes) * quantité_disponible
                    $proportion = ($besoin['quantite'] / $totalDemandes) * $quantiteDisponible;
                    
                    // Prendre la partie entière inférieure (floor)
                    $quantiteAAttribuer = floor($proportion);

                    if ($quantiteAAttribuer > 0) {
                        // Créer le dispatch
                        $this->dispatchRepository->create(
                            $don['id'],
                            $besoin['ville_id'],
                            $don['libelle'],
                            $quantiteAAttribuer
                        );

                        $resultats['attributions'][] = [
                            'don_id' => $don['id'],
                            'type' => $don['type'],
                            'libelle' => $don['libelle'],
                            'ville_nom' => $besoin['ville_nom'],
                            'quantite_attribuee' => $quantiteAAttribuer,
                            'proportion' => $proportion // Pour le tri
                        ];

                        $quantiteDistribuee += $quantiteAAttribuer;
                        $resultats['stats']['quantite_distribuee'] += $quantiteAAttribuer;
                    }
                }

                // Calculer le reste (dû aux arrondis)
                $quantiteRestante = $quantiteDisponible - $quantiteDistribuee;
                if ($quantiteRestante > 0) {
                    $resultats['restes'][] = [
                        'type' => $don['type'],
                        'libelle' => $don['libelle'],
                        'quantite' => $quantiteRestante
                    ];
                    $resultats['stats']['quantite_reste'] += $quantiteRestante;
                }

                if ($quantiteDistribuee > 0) {
                    $resultats['stats']['dons_attribues']++;
                }
            }

            // Trier les attributions par proportion décroissante (plus grandes proportions en premier)
            usort($resultats['attributions'], function($a, $b) {
                return $b['proportion'] <=> $a['proportion'];
            });

            // Calculer les stats par ville et les trier par quantité demandée décroissante
            $resultats['par_ville'] = $this->getStatistiquesParVille();
            usort($resultats['par_ville'], function($a, $b) {
                return $b['demandee'] - $a['demandee'];
            });

        } catch (Exception $e) {
            error_log("Erreur dispatch proportionnel: " . $e->getMessage());
        }

        return $resultats;
    }

    /**
     * Réinitialiser tous les dispatches
     */
    public function reinitialiserDispatches()
    {
        $this->dispatchRepository->deleteAll();
    }

    /**
     * Calculer les statistiques par ville
     */
    private function getStatistiquesParVille()
    {
        $db = Flight::db();
        $stmt = $db->query("
            SELECT 
                v.nom,
                COALESCE(SUM(b.quantite), 0) as demandee,
                COALESCE(SUM(d.quantite_attribuee), 0) as recue
            FROM villes v
            LEFT JOIN besoins b ON b.ville_id = v.id
            LEFT JOIN dispatch d ON d.ville_id = v.id AND d.libelle = b.libelle
            GROUP BY v.id, v.nom
            ORDER BY v.nom
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Calculer les statistiques par ville avec date de première saisie
     */
    private function getStatistiquesParVilleAvecDate()
    {
        $db = Flight::db();
        $stmt = $db->query("
            SELECT 
                v.nom,
                COALESCE(SUM(b.quantite), 0) as demandee,
                COALESCE(SUM(d.quantite_attribuee), 0) as recue,
                MIN(b.date_saisie) as premiere_date
            FROM villes v
            LEFT JOIN besoins b ON b.ville_id = v.id
            LEFT JOIN dispatch d ON d.ville_id = v.id AND d.libelle = b.libelle
            GROUP BY v.id, v.nom
            ORDER BY v.nom
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
