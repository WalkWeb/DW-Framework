<?php

declare(strict_types=1);

namespace NW\App;

use Exception;

class AppException extends Exception
{
    public const CONTROLLER_NOT_FOUND = 'Отсутствует контроллер';
    public const ACTION_NOT_FOUND     = 'Метод не найден';
}
