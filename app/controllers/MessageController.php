<?php
require_once __DIR__ . '/../repositories/MessageRepository.php';
require_once __DIR__ . '/../services/MessageService.php';
require_once __DIR__ . '/../repositories/UserRepository.php';

class MessageController {

  public static function showMessages() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
      Flight::redirect('/login');
      return;
    }

    $pdo = Flight::db();
    $msgRepo = new MessageRepository($pdo);
    $msgSvc = new MessageService($msgRepo);

    $userRepo = new UserRepository($pdo);
    $users = $userRepo->getAll();
    
    // Trouver le nom de l'utilisateur connecte
    $userName = 'Utilisateur';
    foreach ($users as $u) {
      if ($u['id'] == $userId) {
        $userName = $u['prenom'] . ' ' . $u['nom'];
        break;
      }
    }

    // Tous les messages de conversations (pas seulement inbox)
    $messages = $msgSvc->conversations($userId);
    $unread = $msgSvc->unreadCount($userId);
    // mark as read when viewing
    $msgSvc->markReadAll($userId);

    Flight::render('auth/message', [
      'messages' => $messages,
      'users' => $users,
      'current_user' => $userId,
      'unread' => $unread,
      'user_name' => $userName
    ]);
  }

  public static function sendMessage() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) { Flight::json(['ok'=>false,'error'=>'not_logged']); return; }

    $req = Flight::request();
    $to = (int)$req->data->receiver_id;
    $content = trim((string)$req->data->content);
    if ($to <= 0 || $content === '') {
      Flight::json(['ok'=>false,'error'=>'invalid']);
      return;
    }

    $pdo = Flight::db();
    $msgRepo = new MessageRepository($pdo);
    $id = $msgRepo->create($userId, $to, $content);
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    if (strpos($accept, 'application/json') !== false) {
      Flight::json(['ok'=>true,'id'=>$id]);
    } else {
      Flight::redirect('/messages');
    }
  }

  public static function refreshMessages() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) { Flight::json(['ok'=>false]); return; }

    $pdo = Flight::db();
    $msgRepo = new MessageRepository($pdo);
    $msgSvc = new MessageService($msgRepo);
    $messages = $msgSvc->inbox($userId);
    $unread = $msgSvc->unreadCount($userId);
    Flight::json(['ok'=>true,'messages'=>$messages,'unread'=>$unread]);
  }

}
