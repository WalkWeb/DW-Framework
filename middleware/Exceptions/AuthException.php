<?php

namespace Middleware\Exceptions;

use NW\AppException;

class AuthException extends AppException
{
    public const UNAUTHORIZED = 'Вы не авторизованы';
}
