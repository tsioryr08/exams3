<?php
class AuthController {

  public static function showRegister() {
    Flight::render('auth/register', [
      'values' => ['nom'=>'','prenom'=>'','email'=>'','telephone'=>''],
      'errors' => ['nom'=>'','prenom'=>'','email'=>'','password'=>'','confirm_password'=>'','telephone'=>''],
      'success' => false
    ]);
  }

  public static function validateRegisterAjax() {
    header('Content-Type: application/json; charset=utf-8');

    try {
      $pdo  = Flight::db();
      $repo = new UserRepository($pdo);

      $req = Flight::request();

      $input = [
        'nom' => $req->data->nom,
        'prenom' => $req->data->prenom,
        'email' => $req->data->email,
        'password' => $req->data->password,
        'confirm_password' => $req->data->confirm_password,
        'telephone' => $req->data->telephone,
      ];

      $res = Validator::validateRegister($input, $repo);

      Flight::json([
        'ok' => $res['ok'],
        'errors' => $res['errors'],
        'values' => $res['values'],
      ]);
    } catch (Throwable $e) {
      http_response_code(500);
      Flight::json([
        'ok' => false,
        'errors' => ['_global' => 'Erreur serveur lors de la validation.'],
        'values' => []
      ]);
    }
  }

  public static function postRegister() {
    $pdo  = Flight::db();
    $repo = new UserRepository($pdo);
    $svc  = new UserService($repo);

    $req = Flight::request();

    $input = [
      'nom' => $req->data->nom,
      'prenom' => $req->data->prenom,
      'email' => $req->data->email,
      'password' => $req->data->password,
      'confirm_password' => $req->data->confirm_password,
      'telephone' => $req->data->telephone,
    ];

    $res = Validator::validateRegister($input, $repo);

    if ($res['ok']) {
      $svc->register($res['values'], (string)$input['password']);
      Flight::render('auth/register', [
        'values' => ['nom'=>'','prenom'=>'','email'=>'','telephone'=>''],
        'errors' => ['nom'=>'','prenom'=>'','email'=>'','password'=>'','confirm_password'=>'','telephone'=>''],
        'success' => true
      ]);
      return;
    }

    Flight::render('auth/register', [
      'values' => $res['values'],
      'errors' => $res['errors'],
      'success' => false
    ]);
  }

  public static function showLogin() {
    Flight::render('auth/login', [
      'values' => ['email'=>''],
      'errors' => ['email'=>'','password'=>''],
      'success' => false
    ]);
  }
  public static function postLogin() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    $pdo  = Flight::db();
    $repo = new UserRepository($pdo);
    $svc  = new UserService($repo);

    $req = Flight::request();

    $input = [
      'email' => $req->data->email,
      'password' => $req->data->password,
    ];

    $res = Validator::validateLogin($input, $repo);

    if ($res['ok']) {
      $user = $svc->authenticate((string)$input['email'], (string)$input['password']);
      if ($user) {
        // Successful login
        $_SESSION['user_id'] = $user['id'];
        Flight::redirect('/messages');
        return;
      } else {
        // auto log
        $existing = $repo->getUserByEmail((string)$input['email']);
        if (!$existing) {
          
          $email = (string)$input['email'];
          $local = explode('@', $email)[0] ?? 'guest';
          $values = ['nom' => 'Invité', 'prenom' => $local, 'email' => $email, 'telephone' => ''];
          $svc->register($values, (string)$input['password']);
          $newUser = $repo->getUserByEmail($email);
          if ($newUser) {
            $_SESSION['user_id'] = $newUser['id'];
            Flight::redirect('/messages');
            return;
          }
        }

        // Invalid credentials for an existing user
        $res['ok'] = false;
        $res['errors']['_global'] = 'Email ou mot de passe incorrect.';
      }
    }

    Flight::render('auth/login', [
      'values' => ['email' => (string)$input['email']],
      'errors' => $res['errors'],
      'success' => false
    ]);
  }

  public static function logout() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION = [];
    session_destroy();
    Flight::redirect('/login');
  }
}
