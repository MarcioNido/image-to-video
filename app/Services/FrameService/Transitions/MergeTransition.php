<?php

namespace App\Services\FrameService\Transitions;

use App\Enums\VideoSizeEnum;
use App\Services\FrameService\FrameService;
use App\ValueObjects\ImageSectionValueObject;
use Intervention\Image\Interfaces\ImageInterface;

class MergeTransition implements TransitionInterface
{
    public function __invoke(
        ImageInterface $image1,
        ImageSectionValueObject $imageSection,
        VideoSizeEnum $videoSize
    ): array {
        $numberOfFrames = FrameService::FRAMES_PER_SECOND * $imageSection->getTransitionSeconds();

        $factor = 100 / $numberOfFrames;
        $frames = [];

        $image2 = $imageSection->getImageAtFramePosition(0, $videoSize);

        for ($i = 1; $i <= $numberOfFrames; $i++) {
            $clonedImage1 = clone $image1;
            $clonedImage2 = clone $image2;
            $frames[] = $clonedImage1->place($clonedImage2,  'top-left', 0, 0, ($factor * $i));
        }

        return $frames;
    }
}
