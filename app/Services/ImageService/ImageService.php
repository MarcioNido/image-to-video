<?php

namespace App\Services\ImageService;

use App\Enums\VideoSizeEnum;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;

class ImageService
{
    const OVERSIZE_FACTOR = 1.1;
    const OVERSIZE_QUALITY_FACTOR = 2;

    protected ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(Driver::class);
    }

    public function prepareImages(array $array, VideoSizeEnum $size): array
    {
        $imageArray = [];
        foreach ($array as $filePath) {
            $imageArray[] = $this->prepareImage($filePath, $size);
        }

        return $imageArray;
    }

    protected function prepareImage(string $filePath, VideoSizeEnum $size): ImageInterface
    {
        Log::info('Preparing image: ' . $filePath);
        $image = $this->imageManager->read($filePath);
        $image->cover(
            $size->getWidth() * self::OVERSIZE_FACTOR * self::OVERSIZE_QUALITY_FACTOR,
            $size->getHeight() * self::OVERSIZE_FACTOR * self::OVERSIZE_QUALITY_FACTOR,
        );
        return $image;
    }
}
