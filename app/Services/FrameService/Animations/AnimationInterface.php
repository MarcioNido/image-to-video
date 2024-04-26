<?php

namespace App\Services\FrameService\Animations;

use Imagick;
use Intervention\Image\Drivers\Imagick\Core;

interface AnimationInterface
{
    public function __invoke(Imagick|Core $image, int $numberOfFrames): array;
}
