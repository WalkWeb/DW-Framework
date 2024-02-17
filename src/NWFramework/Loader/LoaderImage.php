<?php

namespace NW\Loader;

use NW\Container;

class LoaderImage
{
    // TODO Вынести константы в config.php
    // TODO Добавить фиксированный вариант допустимых расширений, и тоже вынести его в конфиг

    private const IMAGE_MAX_SIZE   = 5242880;
    private const IMAGE_MAX_WEIGHT = 3000;
    private const IMAGE_MAX_HEIGHT = 3000;
    private const DIRECTORY        = '/public/images/upload/';

    private string $filePath;
    private int $errorCode;
    private int $size;

    private bool $testMode;

    private static array $errorMessages = [
        UPLOAD_ERR_INI_SIZE   => LoaderException::ERROR_INI_SIZE,
        UPLOAD_ERR_FORM_SIZE  => LoaderException::ERROR_FORM_SIZE,
        UPLOAD_ERR_PARTIAL    => LoaderException::ERROR_PARTIAL,
        UPLOAD_ERR_NO_FILE    => LoaderException::ERROR_NO_FILE,
        UPLOAD_ERR_NO_TMP_DIR => LoaderException::ERROR_NO_TMP_DIR,
        UPLOAD_ERR_CANT_WRITE => LoaderException::ERROR_CANT_WRITE,
        UPLOAD_ERR_EXTENSION  => LoaderException::ERROR_EXTENSION,
    ];

    public function __construct(Container $container)
    {
        $this->testMode = $container->getAppEnv() === Container::APP_TEST;
    }

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
    public function load(
        array $files,
        int $maxSize = self::IMAGE_MAX_SIZE,
        int $maxWeight = self::IMAGE_MAX_WEIGHT,
        int $maxHeight = self::IMAGE_MAX_HEIGHT,
        string $directory = self::DIRECTORY
    ): Image
    {
        $this->validate($files);
        // TODO Добавить массовую загрузку файлов
        $this->filePath = $files['file']['tmp_name'];
        $this->errorCode = $files['file']['error'];
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
        // TODO Проверка наличия файла
        $this->size = filesize($this->filePath);

        if ($this->size > $maxSize) {
            throw new LoaderException(LoaderException::MAX_SIZE);
        }

        if ($this->errorCode !== UPLOAD_ERR_OK) {
            throw new LoaderException(self::$errorMessages[$this->errorCode] ?? LoaderException::UNKNOWN);
        }

        if (!$this->testMode && !is_uploaded_file($this->filePath)) {
            throw new LoaderException(LoaderException::NO_LOAD_TYPE);
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
            throw new LoaderException(LoaderException::MAX_WIDTH);
        }

        if ($image[1] > $maxHeight) {
            throw new LoaderException(LoaderException::MAX_HEIGHT);
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
            throw new LoaderException(LoaderException::INVALID_TYPE);
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
        // TODO Добавить уникализацию имени
        // TODO Добавить поддиректории хранения
        $name = md5_file($this->filePath);
        $type = image_type_to_extension($image[2]);
        $newPath = DIR . $directory . $name . $type;

        if (!$this->testMode && !move_uploaded_file($this->filePath, $newPath)) {
            throw new LoaderException(LoaderException::FAIL_UPLOAD);
        }

        if ($this->testMode) {
            copy($this->filePath, $newPath);
        }

        return new Image($name, $type, $this->size, $image[0], $image[1], DIR . $directory);
    }

    /**
     * @param array $data
     * @throws LoaderException
     */
    private function validate(array $data): void
    {
        if (!array_key_exists('file', $data) || !is_array($data['file'])) {
            throw new LoaderException(LoaderException::INVALID_FILE_DATA);
        }

        if (!array_key_exists('tmp_name', $data['file']) || !is_string($data['file']['tmp_name'])) {
            throw new LoaderException(LoaderException::INVALID_TMP_NAME);
        }

        if (!array_key_exists('error', $data['file']) || !is_int($data['file']['error'])) {
            throw new LoaderException(LoaderException::INVALID_TMP_ERROR);
        }
    }
}
