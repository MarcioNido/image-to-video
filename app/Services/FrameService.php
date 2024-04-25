<?php

namespace App\Services;

use Imagick;
use ImagickException;

class FrameService
{
    const FRAMES_PER_SECOND = 25;

    protected array $frames = [];

    public function staticFrames(Imagick $image1, $seconds): static
    {
        $this->frames[] = $image1;

        for ($i = 1; $i < $seconds * self::FRAMES_PER_SECOND; $i++) {
            $this->frames[] = $image1;
        }

        return $this;
    }

    /**
     * @throws ImagickException
     */
    public function transitionFrames(Imagick $image1, Imagick $image2, $seconds): static
    {
        $this->frames[] = $image1;

        $frames = $seconds * self::FRAMES_PER_SECOND;

        for ($i = 1; $i < $seconds * self::FRAMES_PER_SECOND; $i++) {
            $image1->setImageOpacity(1 - ($i / $frames));
            $image2->setImageOpacity($i / $frames);
            $this->frames[] = $image1->compositeImage($image2, Imagick::COMPOSITE_OVER, 0, 0);
        }

        return $this;
    }
}
