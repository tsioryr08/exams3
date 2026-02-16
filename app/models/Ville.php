<?php

class Ville {
    private $db;
    
    public function __construct() {
        $this->db = Flight::db();
    }
    
    /**
     * Récupérer toutes les villes
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM villes ORDER BY nom ASC");
        return $stmt->fetchAll();
    }
    
    /**
     * Récupérer une ville par son ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM villes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Créer une nouvelle ville
     */
    public function create($nom, $region) {
        $stmt = $this->db->prepare("INSERT INTO villes (nom, region) VALUES (?, ?)");
        return $stmt->execute([$nom, $region]);
    }
    
    /**
     * Supprimer une ville
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM villes WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Compter le nombre total de villes
     */
    public function count() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM villes");
        $result = $stmt->fetch();
        return $result['total'];
    }
}
