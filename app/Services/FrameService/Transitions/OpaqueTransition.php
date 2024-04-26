<?php

namespace App\Services\FrameService\Transitions;

use Imagick;
use ImagickException;
use Intervention\Image\Drivers\Imagick\Core;

class OpaqueTransition implements TransitionInterface
{
    /**
     * @throws ImagickException
     */
    public function __invoke(Imagick|Core $image1, Imagick|Core $image2, $numberOfFrames): array
    {
        $halfFrames = (int) $numberOfFrames / 2;
        $frames = [];
        $factor = 1 / $halfFrames;

        $clonedImage1 = clone $image1;

        for ($i = 1; $i <= $halfFrames; $i++) {
            $clonedImage1->setImageOpacity(1 - ($factor * $i));
            $frames[] = clone $clonedImage1;
        }

        $clonedImage2 = clone $image2;

        for ($i = 1; $i <= $halfFrames; $i++) {
            $clonedImage2->setImageOpacity($factor * $i);
            $frames[] = clone $clonedImage2;
        }

        return $frames;
    }
}
