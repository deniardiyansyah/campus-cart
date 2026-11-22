<?php

/** @var Router $router */

$router->get('/', 'HomeController@index');

// Contoh yang nanti diaktifkan seiring tahapan:
// $router->get('/login', 'AuthController@showLogin');
// $router->post('/login', 'AuthController@login', ['csrf']);
// $router->get('/produk/{slug}', 'CatalogController@detail');
//
// $router->group(['prefix' => 'seller', 'middleware' => ['auth', 'role:seller,organization,vendor']], function ($r) {
//     $r->get('/', 'Seller\DashboardController@index');
//     $r->get('/produk', 'Seller\ProductController@index');
// });
//
// $router->group(['prefix' => 'moderator', 'middleware' => ['auth', 'role:moderator,admin']], function ($r) {
//     $r->get('/', 'Moderator\DashboardController@index');
// });