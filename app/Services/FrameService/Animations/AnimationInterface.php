<?php

namespace App\Services\FrameService\Animations;

use App\Enums\VideoSizeEnum;
use App\ValueObjects\ImageSectionValueObject;

interface AnimationInterface
{
    public function __invoke(
        ImageSectionValueObject $imageSection,
        VideoSizeEnum $videoSize
    ): array;
}
