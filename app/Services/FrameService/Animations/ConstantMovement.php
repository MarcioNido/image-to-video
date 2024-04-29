<?php

namespace App\Services\FrameService\Animations;

use App\Enums\VideoSizeEnum;
use App\Services\FrameService\FrameService;
use App\ValueObjects\ImageSectionValueObject;

class ConstantMovement implements AnimationInterface
{
    public function __invoke(
        ImageSectionValueObject $imageSection,
        VideoSizeEnum $videoSize
    ): array {

        $numberOfFrames = $imageSection->getSeconds() * FrameService::FRAMES_PER_SECOND;
        $frames = [];

        for ($i = 0; $i < $numberOfFrames; $i++) {
            $frames[] = $imageSection->getImageAtFramePosition($i, $videoSize);
        }

        return $frames;
    }
}
