<?php
class UserService {
  private $repo;
  public function __construct(UserRepository $repo) { $this->repo = $repo; }

  public function register(array $values, $plainPassword) {
    $hash = password_hash((string)$plainPassword, PASSWORD_DEFAULT);
    return $this->repo->create(
      $values['nom'], $values['prenom'], $values['email'], $hash, $values['telephone']
    );
  }

  public function authenticate($email, $plainPassword) {
    $user = $this->repo->getUserByEmail($email);
    if (!$user) return null;
    
    if (password_verify((string)$plainPassword, $user['password_hash'])) {
      return $user;
    }
    return null;
  }
}
