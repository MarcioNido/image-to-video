<?php

namespace App\ValueObjects;

class TextSectionValueObject
{
    public function __construct(
        protected string $text,
        protected int $x,
        protected int $y,
        protected int $startTime,
        protected int $duration
    ) {}

    public function getText(): string
    {
        return $this->text;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function getStartTime(): int
    {
        return $this->startTime;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }
}
