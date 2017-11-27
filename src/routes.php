<?php

namespace TechWilk\Money;

use TechWilk\Money\Controller\DashboardController;
use TechWilk\Money\Controller\TagController;
use TechWilk\Money\Controller\TransactionController;
use TechWilk\Money\Controller\UserController;

// Routes
$app->get('/', DashboardController::class.':getMainDashboard')->setName('home');

$app->post('/user', UserController::class.':postUser')->setName('user-new-post');
$app->get('/user/new', UserController::class.':getUserNew')->setName('user-new-post');
$app->post('/user/{id}/password', UserController::class.':postUserPassword')->setName('user-password-post');
$app->get('/user/{id}', UserController::class.':getUser')->setName('user');

$app->get('/tags', TagController::class.':getTags')->setName('tags');
$app->get('/tags.json', TagController::class.':getTagsJson')->setName('tags-json');
$app->get('/tag/{tag}', TagController::class.':getTag')->setName('tag');

$app->post('/transaction[/{id}]', TransactionController::class.':postTransactionCreate')->setName('transaction-post');
$app->get('/transaction/new', TransactionController::class.':getTransactionNewForm')->setName('transaction-new');
$app->get('/transaction/{id}/edit', TransactionController::class.':getTransactionEditForm')->setName('transaction-edit');
$app->get('/transaction/{id}', TransactionController::class.':getTransaction')->setName('transaction');
$app->get('/transactions[/{account}]', TransactionController::class.':getTransactionsForAccount')->setName('transactions');
$app->get('/transactions/{year}/{month}', TransactionController::class.':getTransactionsForMonth')->setName('month');

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// AUTH
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

$app->get('/login', AuthController::class.':getLogin')->setName('login');
$app->post('/login', AuthController::class.':postLogin')->setName('login-post');
$app->get('/logout', AuthController::class.':getLogout')->setName('logout');
