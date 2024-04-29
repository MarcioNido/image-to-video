<?php

namespace App\ValueObjects;

class ImageValueObject
{
    public function __construct(
        protected string $filePath,
        protected int $width,
        protected int $height,
        protected string $animationType = 'static'
    ) {
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getAnimationType(): string
    {
        return $this->animationType;
    }

}
