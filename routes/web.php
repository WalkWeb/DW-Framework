<?php

$routes = new NW\Route\RouteCollection();

$routes->get('home', '/', 'MainHandler');

$routes->get('posts', '/posts/{page}', 'Post\\PostGetListHandler', ['page' => '\d+']);
$routes->get('post.add', '/post/create', 'Post\\PostAddHandler');
$routes->post('post.create', '/post/create', 'Post\\PostCreateHandler');
$routes->get('post.id', '/post/{id}', 'Post\\PostGetHandler', ['id' => '[a-z0-9-]+']);

$routes->get('cookies', '/cookies', 'Cookie\\CookieGetListHandler');
$routes->post('cookies.add', '/cookies/add', 'Cookie\\CookieAddHandler');
$routes->post('cookies.delete', '/cookies/delete', 'Cookie\\CookieDeleteHandler');

$routes->get('image', '/image', 'Image\\ImageIndexHandler');
$routes->post('image.load', '/image', 'Image\\ImageLoadHandler');
$routes->post('image.loads', '/image_multiple', 'Image\\ImageMultipleLoadHandler');

$routes->get('registration', '/registration', 'User\\UserRegistrationHandler');
$routes->post('registration', '/registration', 'User\\UserCreateHandler');
$routes->get('profile', '/profile', 'User\\UserProfileHandler');
$routes->get('logout', '/logout', 'User\\LogoutHandler');
$routes->get('login', '/login', 'User\\LoginPageHandler');
$routes->post('login', '/login', 'User\\LoginHandler');
$routes->get('change.template', '/change_template/{template}', 'User\\TemplateChangeHandler', ['template' => '[a-z]+']);

$routes
    ->addMiddleware('CreatedByMiddleware')
    ->addMiddleware('StatisticsMiddleware')
    ->addMiddleware('AuthMiddleware')
;

$routes->get('redirect.example', '/redirect', 'RedirectHandler');

return new NW\Route\Router($routes);
