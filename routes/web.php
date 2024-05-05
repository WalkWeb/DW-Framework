<?php

$routes = new NW\Route\RouteCollection();

$routes->get('home', '/', 'Handler\\MainHandler');

$routes->get('posts', '/posts/{page}', 'Handler\\Post\\PostGetListHandler', ['page' => '\d+']);
$routes->get('post.add', '/post/create', 'Handler\\Post\\PostAddHandler');
$routes->post('post.create', '/post/create', 'Handler\\Post\\PostCreateHandler');
$routes->get('post.id', '/post/{id}', 'Handler\\Post\\PostGetHandler', ['id' => '[a-z0-9-]+']);

$routes->get('cookies', '/cookies', 'Handler\\Cookie\\CookieGetListHandler');
$routes->post('cookies.add', '/cookies/add', 'Handler\\Cookie\\CookieAddHandler');
$routes->post('cookies.delete', '/cookies/delete', 'Handler\\Cookie\\CookieDeleteHandler');

$routes->get('image', '/image', 'Handler\\Image\\ImageIndexHandler');
$routes->post('image.load', '/image', 'Handler\\Image\\ImageLoadHandler');
$routes->post('image.loads', '/image_multiple', 'Handler\\Image\\ImageMultipleLoadHandler');

$routes->get('registration', '/registration', 'Handler\\User\\UserRegistrationHandler');
$routes->post('registration', '/registration', 'Handler\\User\\UserCreateHandler');
$routes->get('profile', '/profile', 'Handler\\User\\UserProfileHandler');
$routes->get('logout', '/logout', 'Handler\\User\\LogoutHandler');
$routes->get('login', '/login', 'Handler\\User\\LoginPageHandler');
$routes->post('login', '/login', 'Handler\\User\\LoginHandler');
$routes->get('change.template', '/change_template/{template}', 'Handler\\User\\TemplateChangeHandler', ['template' => '[a-z]+']);
$routes->get('verified.email', '/verified_email', 'Handler\\User\\VerifiedEmailHandler');
$routes->get('check.email', '/check_email/{token}', 'Handler\\User\\CheckEmailHandler', ['token' => '[a-zA-Z0-9-]+']);

$routes
    ->addMiddleware('CreatedByMiddleware')
    ->addMiddleware('StatisticsMiddleware')
    ->addMiddleware('AuthMiddleware')
;

$routes->get('redirect.example', '/redirect', 'Handler\\RedirectHandler');

return new NW\Route\Router($routes);
