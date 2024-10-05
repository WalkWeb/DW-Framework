<?php

declare(strict_types=1);

namespace WalkWeb\NW\Loader;

use Exception;

class LoaderException extends Exception
{
    public const UNKNOWN                    = 'An unknown error occurred while loading the image';
    public const MAX_SIZE                   = 'Image has exceeded maximum weight limit';
    public const INVALID_TYPE               = 'Invalid file type';
    public const INVALID_EXTENSION          = 'Invalid file extension';
    public const MAX_WIDTH                  = 'Image has exceeded maximum width';
    public const MAX_HEIGHT                 = 'Image has exceeded maximum height';
    public const LIMIT_IMAGES               = 'Limit for one-time downloaded images has been exceeded';
    public const NO_LOAD_TYPE               = 'Specified image is not loaded';
    public const INVALID_FILE_DATA          = 'Invalid "file", it required and type array';
    public const INVALID_TMP_NAME           = 'Invalid ["file"]["tmp_name"], it required and type string';
    public const INVALID_TMP_ERROR          = 'Invalid ["file"]["error"], it required and type int';
    public const INVALID_MULTIPLE_TMP_NAME  = 'Invalid ["file"]["tmp_name"], it required and type []string';
    public const INVALID_MULTIPLE_TMP_ERROR = 'Invalid ["file"]["error"], it required and type []int';
    public const FILE_NOT_FOUND             = 'Loaded file not found';
    public const ERROR_INI_SIZE             = 'File size exceeded upload_max_filesize value in PHP configuration';
    public const ERROR_FORM_SIZE            = 'Uploaded file size exceeded the MAX_FILE_SIZE value in the HTML form';
    public const ERROR_PARTIAL              = 'Downloaded file was only partially received';
    public const ERROR_NO_FILE              = 'File was not uploaded';
    public const ERROR_NO_TMP_DIR           = 'Tmp folder is missing';
    public const ERROR_CANT_WRITE           = 'Failed to write file to disk';
    public const ERROR_EXTENSION            = 'PHP extension stopped downloading file';
    public const ERROR_NO_DIRECTORY         = 'Directory for save not found: ';
    public const ERROR_RESIZE_INVALID_TYPE  = 'Invalid type for resize';

    /** Эта ошибка чаще всего возникает когда не хватает прав на сохранение файла в указанную директорию */
    public const FAIL_UPLOAD                = 'An error occurred while loading image while saving to disk';
}
