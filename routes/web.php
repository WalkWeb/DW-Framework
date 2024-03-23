<?php

$routes = new NW\Route\RouteCollection();

$routes->get('home', '/', 'MainHandler')->addMiddleware('CreatedByMiddleware');

$routes->get('posts', '/posts/{page}', 'Post\\PostGetListHandler', ['page' => '\d+']);
$routes->get('post.id', '/post/{id}', 'Post\\PostGetHandler', ['id' => '\d+']);
$routes->get('post.add', '/post/create', 'Post\\PostAddHandler');
$routes->post('post.create', '/post/create', 'Post\\PostCreateHandler');

$routes->get('cookies', '/cookies', 'Cookie\\CookieGetListHandler');
$routes->post('cookies.add', '/cookies/add', 'Cookie\\CookieAddHandler');
$routes->post('cookies.delete', '/cookies/delete', 'Cookie\\CookieDeleteHandler');

$routes->get('redirect.example', '/redirect', 'RedirectHandler');

$routes->get('image', '/image', 'Image\\ImageIndexHandler');
$routes->post('image.load', '/image', 'Image\\ImageLoadHandler');
$routes->post('image.loads', '/image_multiple', 'Image\\ImageMultipleLoadHandler');

//$routes->get('admin', '/admin', 'AdminController@index')->addMiddleware(AuthMiddleware::class);

return new NW\Route\Router($routes);
