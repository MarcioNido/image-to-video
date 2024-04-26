<?php

namespace App\Services\FrameService;

use App\Services\FrameService\Animations\AnimationInterface;
use App\Services\FrameService\Transitions\TransitionInterface;
use Imagick;
use Intervention\Image\Drivers\Imagick\Core;

class FrameService
{
    const FRAMES_PER_SECOND = 25;

    protected array $frames = [];

    public function animate(Imagick|Core $image1, AnimationInterface $animation, float $seconds): static
    {
        $animationFrames = $animation($image1, $seconds * self::FRAMES_PER_SECOND);
        $this->frames = array_merge($this->frames, $animationFrames);
        return $this;
    }

    public function transition(Imagick|Core $image1, Imagick|Core $image2, TransitionInterface $transition, $seconds): static
    {
        $transitionFrames = $transition($image1, $image2, $seconds * self::FRAMES_PER_SECOND);
        $this->frames = array_merge($this->frames, $transitionFrames);
        return $this;
    }

    public function getFrames(): array
    {
        return $this->frames;
    }
}
