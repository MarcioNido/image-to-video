<?php

namespace App\Services\FrameService\Transitions;

use ImagickException;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;

class OpaqueTransition implements TransitionInterface
{
    /**
     * @throws ImagickException
     */
    public function __invoke(ImageInterface $image1, ImageInterface $image2, $numberOfFrames): array
    {
        $halfFrames = (int) $numberOfFrames / 2;
        $frames = [];
        $factor = 1 / $halfFrames;

        $clonedImage1 = $image1->core()->native();

        for ($i = 1; $i <= $halfFrames; $i++) {
            $clonedImage1->setImageOpacity(1 - ($factor * $i));
            $frames[] = ImageManager::imagick()->read($clonedImage1);
        }

        $clonedImage2 = $image2->core()->native();

        for ($i = 1; $i <= $halfFrames; $i++) {
            $clonedImage2->setImageOpacity($factor * $i);
            $frames[] = ImageManager::imagick()->read($clonedImage1);
        }

        return $frames;
    }
}
