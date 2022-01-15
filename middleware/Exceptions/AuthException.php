<?php

namespace Middleware\Exceptions;

use NW\Exception;

class AuthException extends Exception
{
    public const UNAUTHORIZED = 'Вы не авторизованы';
}
