<?php

namespace App\Services\ImageService;

use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\CoreInterface;

class ImageService
{
    const IMAGE_SIZE_1024X576 = '1024X576';

    protected ImageManager $imageManager;


    public function __construct()
    {
        $this->imageManager = new ImageManager(Driver::class);
    }


    public function prepareImages(array $array): array
    {
        $imageArray = [];
        foreach ($array as $filePath) {
            $imageArray[] = $this->prepareImage($filePath);
        }

        return $imageArray;
    }

    protected function prepareImage(string $filePath): CoreInterface
    {
        $image = $this->imageManager->read($filePath);
        $image->cover(1024, 576);
        return $image->core();
    }
}
