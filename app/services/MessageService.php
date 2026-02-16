<?php
class MessageService {
  private $repo;
  public function __construct(MessageRepository $repo) { $this->repo = $repo; }

  // Tous les messages de conversations (envoyes et reçus)
  public function conversations($userId) {
    return $this->repo->getConversationsForUser($userId);
  }

  public function inbox($userId) {
    return $this->repo->getInboxForUser($userId);
  }

  public function unreadCount($userId) {
    return $this->repo->getUnreadCount($userId);
  }

  public function markReadAll($userId) {
    return $this->repo->markAllAsRead($userId);
  }

  public function send($senderId, $receiverId, $content) {
    return $this->repo->create($senderId, $receiverId, $content);
  }
}
