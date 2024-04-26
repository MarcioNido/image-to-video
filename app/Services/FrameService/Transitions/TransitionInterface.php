<?php

namespace App\Services\FrameService\Transitions;

use Imagick;
use Intervention\Image\Drivers\Imagick\Core;

interface TransitionInterface
{
    public function __invoke(Imagick|Core $image1, Imagick|Core $image2, $numberOfFrames): array;
}
