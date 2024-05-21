<?php

namespace App\Services\ImageService;

use App\Enums\VideoSizeEnum;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;

class ImageService
{
    const PERCENTAGE_OVERSIZE = 1.1;

    protected ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(Driver::class);
    }

    public function prepareImages(array $array, VideoSizeEnum $size): array
    {
        Log::info('Preparing images', $array);
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
            $size->getWidth() * self::PERCENTAGE_OVERSIZE,
            $size->getHeight() * self::PERCENTAGE_OVERSIZE,
        );
        return $image;
    }
}
