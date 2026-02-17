<?php

class Ville {
    private $db;
    
    public function __construct() {
        $this->db = Flight::db();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM villes ORDER BY nom ASC");
        return $stmt->fetchAll();
    }
    

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM villes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($nom, $region) {
        $stmt = $this->db->prepare("INSERT INTO villes (nom, region) VALUES (?, ?)");
        return $stmt->execute([$nom, $region]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM villes WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function count() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM villes");
        $result = $stmt->fetch();
        return $result['total'];
    }
}
