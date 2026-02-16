<?php
class MessageRepository {
  private $pdo;
  public function __construct(PDO $pdo) { $this->pdo = $pdo; }

  // Recuperer tous les messages d'une conversation (reçus ET envoyes)
  public function getConversationsForUser($userId, $limit = 100) {
    $limit = (int)$limit;
    // Tous les messages où l'utilisateur est sender OU receiver
    $sql = "SELECT m.*, 
                   u_sender.nom as sender_nom, u_sender.prenom as sender_prenom,
                   u_receiver.nom as receiver_nom, u_receiver.prenom as receiver_prenom
       FROM messages m
       JOIN users u_sender ON u_sender.id = m.sender_id
       JOIN users u_receiver ON u_receiver.id = m.receiver_id
       WHERE m.receiver_id = ? OR m.sender_id = ?
       ORDER BY m.created_at ASC
       LIMIT $limit";
    $st = $this->pdo->prepare($sql);
    $st->execute([(int)$userId, (int)$userId]);
    return $st->fetchAll(PDO::FETCH_ASSOC);
  }

  // Ancienne methode pour les messages reçus uniquement
  public function getInboxForUser($userId, $limit = 100) {
    $limit = (int)$limit;
    $sql = "SELECT m.*, u.nom as sender_nom, u.prenom as sender_prenom
       FROM messages m
       JOIN users u ON u.id = m.sender_id
       WHERE m.receiver_id = ?
       ORDER BY m.created_at DESC
       LIMIT $limit";
    $st = $this->pdo->prepare($sql);
    $st->execute([(int)$userId]);
    return $st->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getUnreadCount($userId) {
    $st = $this->pdo->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
    $st->execute([(int)$userId]);
    return (int)$st->fetchColumn();
  }

  public function markAllAsRead($userId) {
    $st = $this->pdo->prepare("UPDATE messages SET is_read = 1 WHERE receiver_id = ? AND is_read = 0");
    return $st->execute([(int)$userId]);
  }

  public function create($senderId, $receiverId, $content) {
    $st = $this->pdo->prepare(
      "INSERT INTO messages (sender_id, receiver_id, content, is_read, created_at) VALUES (?,?,?,0,NOW())"
    );
    $st->execute([(int)$senderId, (int)$receiverId, (string)$content]);
    return $this->pdo->lastInsertId();
  }


}
