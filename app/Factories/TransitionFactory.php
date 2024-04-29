<?php

namespace App\Factories;

use App\Enums\TransitionTypeEnum;
use App\Services\FrameService\Transitions\MergeTransition;
use App\Services\FrameService\Transitions\TransitionInterface;

class TransitionFactory
{
    public function createTransition(?TransitionTypeEnum $transitionType): ?TransitionInterface
    {
        if (!$transitionType) {
            return null;
        }
        
        return match ($transitionType) {
            TransitionTypeEnum::MERGE => new MergeTransition(),
        };
    }

}
