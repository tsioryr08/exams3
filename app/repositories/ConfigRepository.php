<?php

/**
 * Repository pour gérer la configuration du système
 */
class ConfigRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupérer une valeur de configuration par sa clé
     * @param string $cle
     * @return string|null
     */
    public function getValeur($cle)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT valeur FROM config WHERE cle = ?");
            $stmt->execute([$cle]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['valeur'] : null;
        } catch (Exception $e) {
            error_log("Erreur récupération config: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupérer le pourcentage de frais d'achat
     * @return float
     */
    public function getFraisAchatPourcentage()
    {
        $valeur = $this->getValeur('frais_achat_pourcentage');
        return $valeur ? (float)$valeur : 10.0; // 10% par défaut
    }

    /**
     * Mettre à jour une valeur de configuration
     * @param string $cle
     * @param string $valeur
     * @return bool
     */
    public function updateValeur($cle, $valeur)
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE config 
                SET valeur = ?, date_modification = NOW() 
                WHERE cle = ?
            ");
            $stmt->execute([$valeur, $cle]);
            return true;
        } catch (Exception $e) {
            error_log("Erreur mise à jour config: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer toute la configuration
     * @return array
     */
    public function getAll()
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM config ORDER BY cle");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur récupération config: " . $e->getMessage());
            return [];
        }
    }
}
