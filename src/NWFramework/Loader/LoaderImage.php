<?php

namespace NW\Loader;

class LoaderImage
{
    // TODO Вынести константы в config.php

    private const IMAGE_MAX_SIZE   = 5242880;
    private const IMAGE_MAX_WEIGHT = 3000;
    private const IMAGE_MAX_HEIGHT = 3000;
    private const DIRECTORY        = '/public/images/upload/';

    private $filePath;
    private $errorCode;
    private $size;

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
     * @param int $maxSize
     * @param int $maxWeight
     * @param int $maxHeight
     * @param string $directory
     * @return Image
     * @throws LoaderException
     */
    public function loaderImage(
        array $files,
        int $maxSize = self::IMAGE_MAX_SIZE,
        int $maxWeight = self::IMAGE_MAX_WEIGHT,
        int $maxHeight = self::IMAGE_MAX_HEIGHT,
        string $directory = self::DIRECTORY
    ): Image
    {
        $this->filePath = $files['upload']['tmp_name'] ?? $files['file']['tmp_name'];
        $this->errorCode = $files['upload']['error'] ?? $files['file']['error'];
        $this->preloadValidate($maxSize);
        $image = $this->upload();
        $this->validateSize($image, $maxWeight, $maxHeight);
        return $this->save($image, $directory);
    }

    /**
     * @param int $maxSize
     * @throws LoaderException
     */
    private function preloadValidate(int $maxSize): void
    {
        $this->size = filesize($this->filePath);

        if ($this->size > $maxSize) {
            throw new LoaderException(LoaderException::ERROR_SIZE);
        }

        if ($this->errorCode !== UPLOAD_ERR_OK || !is_uploaded_file($this->filePath)) {
            throw new LoaderException(self::$errorMessages[$this->errorCode] ?? LoaderException::ERROR_UNKNOWN);
        }
    }

    /**
     * @param string $mime
     * @return bool
     */
    private function validateType(string $mime): bool
    {
        return strpos($mime, 'image') === false;
    }

    /**
     * @param array $image
     * @param int $maxWeight
     * @param int $maxHeight
     * @throws LoaderException
     */
    private function validateSize(array $image, int $maxWeight, int $maxHeight): void
    {
        if ($image[0] > $maxWeight) {
            throw new LoaderException(LoaderException::ERROR_WIDTH);
        }

        if ($image[1] > $maxHeight) {
            throw new LoaderException(LoaderException::ERROR_HEIGHT);
        }
    }

    /**
     * @return array
     * @throws LoaderException
     */
    private function upload(): array
    {
        $FileInfo = finfo_open(FILEINFO_MIME_TYPE);

        $mime = (string)finfo_file($FileInfo, $this->filePath);

        finfo_close($FileInfo);

        if ($this->validateType($mime)) {
            throw new LoaderException(LoaderException::ERROR_TYPE);
        }

        return getimagesize($this->filePath);
    }

    /**
     * @param array $image
     * @param string $directory
     * @return Image
     * @throws LoaderException
     */
    private function save(array $image, string $directory): Image
    {
        $name = md5_file($this->filePath);

        $type = image_type_to_extension($image[2]);

        if (!move_uploaded_file($this->filePath, DIR . $directory . $name . $type)) {
            throw new LoaderException(LoaderException::ERROR_UPLOAD);
        }

        return new Image($name, $type, $this->size, $image[0], $image[1], $directory);
    }
}
