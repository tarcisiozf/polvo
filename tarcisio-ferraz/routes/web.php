<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;

$router->get('/', function () use ($router) {
    return view('index');
});

/*
    CRUD DE PRODUTO
*/
// LER
$router->get('/products', 'ProductController@selectAll');

// INSERIR
$router->post('/products', 'ProductController@create');

// ATUALIZAR
$router->put('/products/{id}', 'ProductController@update');

// DELETAR
$router->delete('/products/{id}', 'ProductController@delete');

/*
    CRUD DE PEDIDO
*/
// LER
$router->get('/orders', 'OrderController@selectAll');

// INSERIR
$router->post('/orders', 'OrderController@create');

// ATUALIZAR
$router->put('/orders/{id}', 'OrderController@update');

// DELETAR
$router->delete('/orders/{id}', 'OrderController@delete');