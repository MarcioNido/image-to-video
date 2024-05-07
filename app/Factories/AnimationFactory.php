<?php

namespace App\Factories;

use App\Enums\AnimationTypeEnum;
use App\Services\FrameService\Animations\AnimationInterface;
use App\Services\FrameService\Animations\ConstantMovement;
use App\Services\FrameService\Animations\StaticImage;

class AnimationFactory
{
    public function createAnimation(AnimationTypeEnum $animationType): AnimationInterface
    {
        return match ($animationType) {
            AnimationTypeEnum::TOP_LEFT_TO_BOTTOM_RIGHT,
            AnimationTypeEnum::TOP_RIGHT_TO_BOTTOM_LEFT,
            AnimationTypeEnum::BOTTOM_LEFT_TO_TOP_RIGHT,
            AnimationTypeEnum::BOTTOM_RIGHT_TO_TOP_LEFT,
            AnimationTypeEnum::CENTER_ZOOM_OUT,
            AnimationTypeEnum::CENTER_ZOOM_IN,
            AnimationTypeEnum::TOP_LEFT_ZOOM_OUT,
            AnimationTypeEnum::TOP_LEFT_ZOOM_IN => new ConstantMovement(),
            default => new StaticImage(),
        };
    }
}
