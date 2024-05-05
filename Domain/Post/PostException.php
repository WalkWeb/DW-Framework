<?php

namespace Domain\Post;

use WalkWeb\NW\AppException;

class PostException extends AppException
{
    public const INVALID_ID          = 'Incorrect parameter "id", it required and type string';
    public const INVALID_ID_VALUE    = 'Incorrect parameter "id", excepted uuid';
    public const NOT_FOUND           = 'Указанного поста не существует';
    public const INVALID_TITLE       = 'Incorrect parameter "title", it required and type string';
    public const INVALID_TITLE_VALUE = 'Incorrect parameter "title", should be min-max length: ';
    public const INVALID_SLUG        = 'Incorrect parameter "slug", it required and type string';
    public const INVALID_SLUG_VALUE  = 'Incorrect parameter "slug", should be min-max length: ';
    public const INVALID_TEXT        = 'Incorrect parameter "text", it required and type string';
    public const INVALID_TEXT_VALUE  = 'Incorrect parameter "text", should be min-max length: ';
}
