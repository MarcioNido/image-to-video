<?php

namespace App\Enums;

enum VideoSizeEnum: string
{
    case SMALL = '320x180';
    case MEDIUM = '640x360';
    case LARGE = '720x405';
    case HD = '1280x720';
    case FULL_HD = '1920x1080';

    public function getWidth(): int
    {
        return (int) explode('x', $this->value)[0];
    }

    public function getHeight(): int
    {
        return (int) explode('x', $this->value)[1];
    }
}
