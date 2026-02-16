<?php
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/services/Validator.php';
require_once __DIR__ . '/services/UserService.php';
require_once __DIR__ . '/repositories/UserRepository.php';
require_once __DIR__ . '/controllers/MessageController.php';
require_once __DIR__ . '/repositories/MessageRepository.php';
require_once __DIR__ . '/services/MessageService.php';
// Admin
require_once __DIR__ . '/controllers/AdminController.php';
require_once __DIR__ . '/repositories/CategoryRepository.php';

Flight::route('GET /', function () {
    Flight::render('home');
});

Flight::route('GET /register', ['AuthController', 'showRegister']);
Flight::route('POST /register', ['AuthController', 'postRegister']);
Flight::route('POST /api/validate/register', ['AuthController', 'validateRegisterAjax']);

// Messages
Flight::route('GET /messages', ['MessageController', 'showMessages']);
Flight::route('POST /messages/send', ['MessageController', 'sendMessage']);
Flight::route('GET /messages/refresh', ['MessageController', 'refreshMessages']);

//login
Flight::route('GET /login', ['AuthController', 'showLogin']);
Flight::route('POST /login', ['AuthController', 'postLogin']);
Flight::route('GET /logout', ['AuthController', 'logout']);

// Admin routes
Flight::route('GET /admin/login', ['AdminController', 'showLogin']);
Flight::route('POST /admin/login', ['AdminController', 'processLogin']);
Flight::route('GET /admin', ['AdminController', 'dashboard']);
Flight::route('GET /admin/logout', ['AdminController', 'logout']);
Flight::route('GET /admin/categories', ['AdminController', 'listCategories']);
Flight::route('POST /admin/categories', ['AdminController', 'createCategory']);
Flight::route('GET /admin/categories/edit/@id', ['AdminController', 'showEditCategory']);
Flight::route('POST /admin/categories/edit/@id', ['AdminController', 'updateCategory']);
Flight::route('POST /admin/categories/delete/@id', ['AdminController', 'deleteCategory']);
