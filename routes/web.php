<?php

$routes = new NW\Route\RouteCollection();

$routes->get('home', '/', 'MainController@index');
$routes->get('posts', '/posts/{page}', 'PostController@index', ['page' => '\d+']);
$routes->get('post.id', '/post/{id}', 'PostController@view', ['id' => '\d+']);
$routes->get('post.add', '/post/create', 'PostController@add');
$routes->post('post.create', '/post/create', 'PostController@create');
$routes->get('admin', '/admin', 'AdminController@index')->middleware('AuthMiddleware');

$router = new NW\Route\Router($routes);
