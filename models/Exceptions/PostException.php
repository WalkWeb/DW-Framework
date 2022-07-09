<?php

namespace Models\Exceptions;

use NW\AppException;

class PostException extends AppException
{
    public const NOT_FOUND = 'Указанного поста не существует';
}
