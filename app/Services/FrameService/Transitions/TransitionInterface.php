<?php

namespace App\Services\FrameService\Transitions;

use App\Enums\VideoSizeEnum;
use App\ValueObjects\ImageSectionValueObject;
use Intervention\Image\Interfaces\ImageInterface;

interface TransitionInterface
{
    public function __invoke(
        ImageInterface $image1,
        ImageSectionValueObject $imageSection,
        VideoSizeEnum $videoSize
    ): array;
}
