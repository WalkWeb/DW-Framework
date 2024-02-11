<?php

$routes = new NW\Route\RouteCollection();

$routes->get('home', '/', 'MainController@index');

$routes->get('posts', '/posts/{page}', 'PostController@index', ['page' => '\d+']);
$routes->get('post.id', '/post/{id}', 'PostController@view', ['id' => '\d+']);
$routes->get('post.add', '/post/create', 'PostController@add');
$routes->post('post.create', '/post/create', 'PostController@create');

$routes->get('cookies', '/cookies', 'CookieController@index');
$routes->post('cookies.add', '/cookies/add', 'CookieController@add');
$routes->post('cookies.delete', '/cookies/delete', 'CookieController@delete');

$routes->get('redirect.example', '/redirect', 'MainController@redirectExample');

$routes->get('image', '/image', 'ImageController@index');
$routes->post('image.load', '/image', 'ImageController@load');

//$routes->get('admin', '/admin', 'AdminController@index')->addMiddleware(AuthMiddleware::class);

return new NW\Route\Router($routes);
