<?php

namespace NW\Loader;

use DateTime;
use Exception;
use NW\Container;
use NW\Traits\StringTrait;
use RuntimeException;

class LoaderImage
{
    use StringTrait;

    private const IMAGE_MAX_SIZE   = 5242880;
    private const IMAGE_MAX_WEIGHT = 3000;
    private const IMAGE_MAX_HEIGHT = 3000;
    private const LIMIT_IMAGES     = 10;
    private const DIRECTORY        = '/public/images/upload/';
    public const FRONT_DIRECTORY   = '/images/upload/';
    private const FILE_EXTENSION   = ['jpg', 'jpeg', 'gif', 'png'];

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
     * @param string[] $fileExtension
     * @return Image
     * @throws Exception
     */
    public function load(
        array $files,
        int $maxSize = self::IMAGE_MAX_SIZE,
        int $maxWeight = self::IMAGE_MAX_WEIGHT,
        int $maxHeight = self::IMAGE_MAX_HEIGHT,
        string $directory = self::DIRECTORY,
        array $fileExtension = self::FILE_EXTENSION
    ): Image
    {
        $this->validate($files);
        $filePath = $files['file']['tmp_name'];
        $this->checkError($files['file']['error']);
        $imageInfo = $this->preloadValidate($filePath, $maxSize, $maxWeight, $maxHeight, $fileExtension);
        return $this->save($filePath, $imageInfo, $directory);
    }

    /**
     * Загрузка сразу нескольких картинок
     *
     * @param array $files
     * @param int $maxSize
     * @param int $maxWeight
     * @param int $maxHeight
     * @param string $directory
     * @param string[] $fileExtension
     * @param int $limitImages
     * @return ImageCollection
     * @throws Exception
     */
    public function multipleLoad(
        array $files,
        int $maxSize = self::IMAGE_MAX_SIZE,
        int $maxWeight = self::IMAGE_MAX_WEIGHT,
        int $maxHeight = self::IMAGE_MAX_HEIGHT,
        string $directory = self::DIRECTORY,
        array $fileExtension = self::FILE_EXTENSION,
        int $limitImages = self::LIMIT_IMAGES
    ): ImageCollection
    {
        $this->multipleValidate($files);
        $loadImages = new ImageCollection();
        $data = [];

        if (count($files['file']['tmp_name']) > $limitImages) {
            throw new LoaderException(LoaderException::LIMIT_IMAGES);
        }

        foreach ($files['file']['tmp_name'] as $i => $tmpName) {
            $data[] = [
                'file' => [
                    'tmp_name' => $files['file']['tmp_name'][$i],
                    'error' => $files['file']['error'][$i],
                ],
            ];
        }

        foreach ($data as $item) {
            $loadImages->add($this->load($item, $maxSize, $maxWeight, $maxHeight, $directory, $fileExtension));
        }

        return $loadImages;
    }

    /**
     * @param int $errorCode
     * @throws LoaderException
     */
    private function checkError(int $errorCode): void
    {
        if ($errorCode !== UPLOAD_ERR_OK) {
            throw new LoaderException(self::$errorMessages[$errorCode] ?? LoaderException::UNKNOWN);
        }
    }

    /**
     * @param string $filePath
     * @param int $maxSize
     * @param int $maxWeight
     * @param int $maxHeight
     * @param array $fileExtension
     * @return array
     * @throws LoaderException
     */
    private function preloadValidate(string $filePath, int $maxSize, int $maxWeight, int $maxHeight, array $fileExtension): array
    {
        if (!file_exists($filePath)) {
            throw new LoaderException(LoaderException::FILE_NOT_FOUND);
        }

        // TODO Подумать, можно ли избавиться от finfo_open/finfo_close
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = (string)finfo_file($fileInfo, $filePath);
        finfo_close($fileInfo);

        if (strpos($mime, 'image') === false) {
            throw new LoaderException(LoaderException::INVALID_TYPE);
        }

        if (!in_array(explode('/', $mime)[1], $fileExtension, true)) {
            throw new LoaderException(LoaderException::INVALID_EXTENSION);
        }

        $imageInfo = getimagesize($filePath);

        if ($imageInfo[0] > $maxWeight) {
            throw new LoaderException(LoaderException::MAX_WIDTH);
        }

        if ($imageInfo[1] > $maxHeight) {
            throw new LoaderException(LoaderException::MAX_HEIGHT);
        }

        $size = filesize($filePath);

        if ($size > $maxSize) {
            throw new LoaderException(LoaderException::MAX_SIZE);
        }

        if (!$this->testMode && !is_uploaded_file($filePath)) {
            throw new LoaderException(LoaderException::NO_LOAD_TYPE);
        }

        $imageInfo['size'] = $size;

        return $imageInfo;
    }

    /**
     * @param string $filePath
     * @param array $image
     * @param string $directory
     * @return Image
     * @throws Exception
     */
    private function save(string $filePath, array $image, string $directory): Image
    {
        $date = new DateTime();
        $dirSuffix = $date->format('Y') . '/' . $date->format('m') . '/' . $date->format('d') . '/';
        $directory .= $dirSuffix;

        $dPath = DIR . $directory;
        if (!is_dir($dPath)) {
            try {
                if (!mkdir($dPath, 0755, true) && !is_dir($dPath)) {
                    throw new RuntimeException(sprintf('Directory "%s" was not created', $dPath));
                }
            } catch (Exception $e) {
                throw new RuntimeException($e->getMessage());
            }
        }

        $name = self::generateString(10);
        $type = image_type_to_extension($image[2]);
        $newPath = DIR . $directory . $name . $type;

        if (!$this->testMode && !move_uploaded_file($filePath, $newPath)) {
            throw new LoaderException(LoaderException::FAIL_UPLOAD);
        }

        if ($this->testMode) {
            copy($filePath, $newPath);
        }

        return new Image(
            $name,
            $type,
            $image['size'],
            $image[0],
            $image[1],
            $newPath,
            self::FRONT_DIRECTORY . $dirSuffix . $name . $type
        );
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

    /**
     * @param array $data
     * @throws LoaderException
     */
    private function multipleValidate(array $data): void
    {
        if (!array_key_exists('file', $data) || !is_array($data['file'])) {
            throw new LoaderException(LoaderException::INVALID_FILE_DATA);
        }

        if (!array_key_exists('tmp_name', $data['file']) || !is_array($data['file']['tmp_name'])) {
            throw new LoaderException(LoaderException::INVALID_MULTIPLE_TMP_NAME);
        }

        foreach ($data['file']['tmp_name'] as $tmpName) {
            if (!is_string($tmpName)) {
                throw new LoaderException(LoaderException::INVALID_MULTIPLE_TMP_NAME);
            }
        }

        if (!array_key_exists('error', $data['file']) || !is_array($data['file']['error'])) {
            throw new LoaderException(LoaderException::INVALID_MULTIPLE_TMP_ERROR);
        }

        foreach ($data['file']['error'] as $error) {
            if (!is_int($error)) {
                throw new LoaderException(LoaderException::INVALID_MULTIPLE_TMP_ERROR);
            }
        }
    }
}
