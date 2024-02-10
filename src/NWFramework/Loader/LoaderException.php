<?php

declare(strict_types=1);

namespace NW\Loader;

use Exception;

class LoaderException extends Exception
{
    public const ERROR_UNKNOWN = 'При загрузке изображения произошла неизвестная ошибка';
    public const ERROR_SIZE    = 'Изображение превысило максимально допустимый вес';
    public const ERROR_TYPE    = 'Недопустимый тип файла';
    public const ERROR_WIDTH   = 'Изображение превысило максимальную ширину';
    public const ERROR_HEIGHT  = 'Изображение превысило максимальную высоту';
    public const ERROR_NO_LOAD = 'Указанное изображение не является загруженнным';

    /** Эта ошибка чаще всего возникает когда не хватает прав на сохранение файла в указанную директорию */
    public const ERROR_UPLOAD = 'При загрузке изображения произошла ошибка сохранения на диск';
}
