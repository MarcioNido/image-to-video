<?php

namespace App\Services\FrameService\Animations;

use App\Enums\VideoSizeEnum;
use App\Services\FrameService\FrameService;
use App\ValueObjects\ImageSectionValueObject;

class StaticImage implements AnimationInterface
{
    public function __invoke(
        ImageSectionValueObject $imageSection,
        VideoSizeEnum $videoSize
    ): array {

        $image = $imageSection->getImageAtFramePosition(0, $videoSize);
        $numberOfFrames = $imageSection->getSeconds() * FrameService::FRAMES_PER_SECOND;
        $frames = [];

        for ($i = 1; $i <= $numberOfFrames; $i++) {
            $frames[] = $image;
        }

        return $frames;
    }
}
