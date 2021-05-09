<?php

namespace NW\Loader;

class LoaderImage
{
    private const ERROR_UNKNOWN = 'При загрузке изображения произошла неизвестная ошибка';
    private const ERROR_SIZE    = 'Изображение превысило максимально допустимый вес';
    private const ERROR_TYPE    = 'Недопустимый тип файла';
    private const ERROR_WIDTH   = 'Изображение превысило максимальную ширину';
    private const ERROR_HEIGHT  = 'Изображение превысило максимальную высоту';

    /** Эта ошибка чаще всего возникает когда не хватает прав на сохранение файла в указанную директорию */
    private const ERROR_UPLOAD = 'При загрузке изображения произошла ошибка сохранения на диск';

    private $filePath;
    private $errorCode;
    private $size;
    private $baseDir = '/public/';

    private static $errorMessages = [
        UPLOAD_ERR_INI_SIZE   => 'Размер файла превысил значение upload_max_filesize в конфигурации PHP',
        UPLOAD_ERR_FORM_SIZE  => 'Размер загружаемого файла превысил значение MAX_FILE_SIZE в HTML-форме',
        UPLOAD_ERR_PARTIAL    => 'Загружаемый файл был получен только частично',
        UPLOAD_ERR_NO_FILE    => 'Файл не был загружен',
        UPLOAD_ERR_NO_TMP_DIR => 'Отсутствует временная папка',
        UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл на диск',
        UPLOAD_ERR_EXTENSION  => 'PHP-расширение остановило загрузку файла',
    ];

    /**
     * Сохраняет картинку на сервере и возвращает её название
     *
     * @param array $files
     * @param int $max_size
     * @param int|null $max_weight
     * @param int|null $max_height
     * @param string $directory
     * @return Image
     */
    public function loaderImage(
        array $files,
        int $max_size = 5242880,
        int $max_weight = null,
        int $max_height = null,
        string $directory = 'images/upload/'
    ): Image
    {
        $this->filePath = $files['upload']['tmp_name'] ?? $files['file']['tmp_name'];
        $this->errorCode = $files['upload']['error'] ?? $files['file']['error'];
        $max_size = $max_size ?? UPLOAD_IMAGE_MAX_SIZE;
        $max_weight = $max_weight ?? UPLOAD_IMAGE_MAX_WEIGHT;
        $max_height = $max_height ?? UPLOAD_IMAGE_MAX_HEIGHT;
        $this->preloadValidate($max_size);
        $image = $this->upload();
        $this->validateSize($image, $max_weight, $max_height);
        return $this->save($image, $directory);
    }

    /**
     * @param int $max_size
     */
    private function preloadValidate(int $max_size): void
    {
        $this->size = filesize($this->filePath);

        if ($this->size > $max_size) {
            die(self::ERROR_SIZE);
        }

        if ($this->errorCode !== UPLOAD_ERR_OK || !is_uploaded_file($this->filePath)) {
            die(self::$errorMessages[$this->errorCode] ?? self::ERROR_UNKNOWN);
        }
    }

    /**
     * @param $mime
     * @return bool
     */
    private function validateType($mime): bool
    {
        return strpos($mime, 'image') === false;
    }

    /**
     * @param $image
     * @param int $max_weight
     * @param int $max_height
     */
    private function validateSize($image, int $max_weight, int $max_height): void
    {
        if ($image[0] > $max_weight) {
            die(self::ERROR_WIDTH);
        }

        if ($image[1] > $max_height) {
            die(self::ERROR_HEIGHT);
        }
    }

    /**
     * @return array
     */
    private function upload(): array
    {
        $FileInfo = finfo_open(FILEINFO_MIME_TYPE);

        $mime = (string)finfo_file($FileInfo, $this->filePath);

        finfo_close($FileInfo);

        if ($this->validateType($mime)) {
            die(self::ERROR_TYPE);
        }

        return getimagesize($this->filePath);
    }

    /**
     * @param $image
     * @param string $directory
     * @return Image
     */
    private function save($image, string $directory): Image
    {
        $name = md5_file($this->filePath);

        $type = image_type_to_extension($image[2]);

        if (!move_uploaded_file($this->filePath, DIR . $this->baseDir . $directory . $name . $type)) {
            // todo throw new
            die(self::ERROR_UPLOAD);
        }

        return new Image($name, $type, $this->size, $directory, $image[0], $image[1]);
    }
}
