<?php

namespace Models\Post;

use NW\AppException;

class PostException extends AppException
{
    public const NOT_FOUND           = 'Указанного поста не существует';
    public const INVALID_TITLE       = 'Incorrect parameter "title", it required and type string';
    public const INVALID_TITLE_VALUE = 'Incorrect parameter "title", should be min-max length: ';
    public const INVALID_TEXT        = 'Incorrect parameter "text", it required and type string';
    public const INVALID_TEXT_VALUE  = 'Incorrect parameter "text", should be min-max length: ';
}
