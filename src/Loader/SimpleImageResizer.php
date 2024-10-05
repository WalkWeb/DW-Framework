<?php

declare(strict_types=1);

namespace WalkWeb\NW\Loader;

use DateTime;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Traits\StringTrait;

class SimpleImageResizer
{
    use StringTrait;

    public const DIRECTORY       = '/public/images/upload/';
    public const FRONT_DIRECTORY = '/images/upload/';
    public const QUALITY         = 40;
    public const NAME_LENGTH     = 10;
    public const EXTENSION       = '.jpg';

    /**
     * @param Image $image
     * @param int $maxWidth
     * @param int $maxHeight
     * @param int $quality
     * @param string $directory
     * @return string
     * @throws AppException
     */
    public static function resize(
        Image $image,
        int $maxWidth,
        int $maxHeight,
        int $quality = self::QUALITY,
        string $directory = self::DIRECTORY
    ): string
    {
        if ($image->getWidth() <= $maxWidth && $image->getHeight() <= $maxHeight) {
            return $image->getFilePath();
        }

        $reduce = self::calculateResize($image, $maxWidth, $maxHeight);

        $date = new DateTime();
        $dirSuffix = $date->format('Y') . '/' . $date->format('m') . '/' . $date->format('d') . '/';
        $name = self::generateString(self::NAME_LENGTH) . self::EXTENSION;

        $absoluteDir = DIR . $directory . $dirSuffix;
        $absolutePath =  $absoluteDir. $name;
        $path = self::FRONT_DIRECTORY . $dirSuffix . $name;

        if (!file_exists($absoluteDir)) {
            throw new AppException(LoaderException::ERROR_NO_DIRECTORY . $absoluteDir);
        }

        self::createResizeImage($image, $reduce, $absolutePath, $quality);

        return $path;
    }

    /**
     * @param Image $image
     * @param int $maxWidth
     * @param int $maxHeight
     * @return float
     */
    private static function calculateResize(Image $image, int $maxWidth, int $maxHeight): float
    {
        $widthReduce = $maxWidth / $image->getWidth();
        $heightReduce = $maxHeight / $image->getHeight();

        return $widthReduce > $heightReduce ? $heightReduce : $widthReduce;
    }

    /**
     * @param Image $image
     * @param float $reduce
     * @param string $absolutePath
     * @param int $quality
     */
    private static function createResizeImage(Image $image, float $reduce, string $absolutePath, int $quality): void
    {
        $newWidth = (int)($image->getWidth() * $reduce);
        $newHeight = (int)($image->getHeight() * $reduce);

        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        $source = imagecreatefromjpeg($image->getAbsoluteFilePath());

        imagecopyresized(
            $thumb,
            $source, 0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $image->getWidth(),
            $image->getHeight()
        );

        imagejpeg($thumb, $absolutePath, $quality);
    }
}
