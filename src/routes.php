<?php

namespace TechWilk\Money;

use TechWilk\Money\Controller\AuthController;
use TechWilk\Money\Controller\DashboardController;
use TechWilk\Money\Controller\TagController;
use TechWilk\Money\Controller\TransactionController;
use TechWilk\Money\Controller\UserController;
use DateTime;

// Routes
$app->get('/', DashboardController::class.':getMainDashboard')->setName('home');

$app->group('/user', function() {
    $this->post('', UserController::class.':postUser')->setName('user-new-post');
    $this->get('/new', UserController::class.':getUserNew')->setName('user-new-post');
    $this->post('/{id}/password', UserController::class.':postUserPassword')->setName('user-password-post');
    $this->get('/{id}', UserController::class.':getUser')->setName('user');
});

$app->group('/tag', function() {
    $this->get('s', TagController::class.':getTags')->setName('tags');
    $this->get('s.json', TagController::class.':getTagsJson')->setName('tags-json');
    $this->get('/{tag}', TagController::class.':getTag')->setName('tag');
});

$app->group('/transaction', function() {
    $this->post('[/{id}]', TransactionController::class.':postTransaction')->setName('transaction-post');
    $this->get('/new', TransactionController::class.':getTransactionNewForm')->setName('transaction-new');
    $this->get('/{id}/edit', TransactionController::class.':getTransactionEditForm')->setName('transaction-edit');
    $this->get('/{id}', TransactionController::class.':getTransaction')->setName('transaction');
    $this->get('s[/{account}]', TransactionController::class.':getTransactionsForAccount')->setName('transactions');
    $this->get('s/{year}/{month}', TransactionController::class.':getTransactionsForMonth')->setName('month');
});

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// AUTH
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

$app->get('/login', AuthController::class.':getLogin')->setName('login');
$app->post('/login', AuthController::class.':postLogin')->setName('login-post');
$app->get('/logout', AuthController::class.':getLogout')->setName('logout');
