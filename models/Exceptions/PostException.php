<?php

namespace Models\Exceptions;

use NW\Exception;

class PostException extends Exception
{
    public const NOT_FOUND = 'Указанного поста не существует';
}
