<?php

require_once __DIR__ . '/../app/Core/Router.php';

$router = new Router();
$router->get('/checkout', 'CheckoutController@index');
$router->post('/checkout/verify', 'CheckoutController@verify');
$router->get('/admin/payments', 'Admin/PaymentsController@index');
$router->dispatch();
