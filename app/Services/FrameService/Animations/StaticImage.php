<?php

namespace App\Services\FrameService\Animations;

use Imagick;
use Intervention\Image\Drivers\Imagick\Core;

class StaticImage implements AnimationInterface
{
    public function __invoke(Imagick|Core $image, $numberOfFrames): array
    {
        $frames = [];
        $clonedImage = clone $image;

        for ($i = 1; $i <= $numberOfFrames; $i++) {
            $frames[] = $clonedImage;
        }

        return $frames;
    }
}
