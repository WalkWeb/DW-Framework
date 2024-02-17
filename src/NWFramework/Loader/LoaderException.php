<?php

declare(strict_types=1);

namespace NW\Loader;

use Exception;

class LoaderException extends Exception
{
    // TODO Перевести все в английский
    // TODO Убрать ERROR_ - здесь все ошибки
    public const ERROR_UNKNOWN     = 'При загрузке изображения произошла неизвестная ошибка';
    public const ERROR_SIZE        = 'Изображение превысило максимально допустимый вес';
    public const ERROR_TYPE        = 'Недопустимый тип файла';
    public const ERROR_WIDTH       = 'Изображение превысило максимальную ширину';
    public const ERROR_HEIGHT      = 'Изображение превысило максимальную высоту';
    public const ERROR_NO_LOAD     = 'Указанное изображение не является загруженным';
    public const INVALID_FILE_DATA = 'Invalid "file", it required and type array';
    public const INVALID_TMP_NAME  = 'Invalid ["file"]["tmp_name"], it required and type string';
    public const INVALID_TMP_ERROR = 'Invalid ["file"]["error"], it required and type int';

    /** Эта ошибка чаще всего возникает когда не хватает прав на сохранение файла в указанную директорию */
    public const ERROR_UPLOAD      = 'При загрузке изображения произошла ошибка сохранения на диск';
}
