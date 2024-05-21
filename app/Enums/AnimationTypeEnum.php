<?php

namespace App\Enums;

use Illuminate\Support\Arr;

enum AnimationTypeEnum: string
{
    case TOP_LEFT_TO_BOTTOM_RIGHT = 'top_left_to_bottom_right';
    case TOP_RIGHT_TO_BOTTOM_LEFT = 'top_right_to_bottom_left';
    case BOTTOM_LEFT_TO_TOP_RIGHT = 'bottom_left_to_top_right';
    case BOTTOM_RIGHT_TO_TOP_LEFT = 'bottom_right_to_top_left';
    case TOP_LEFT_ZOOM_OUT = 'top_left_zoom_out';
    case TOP_LEFT_ZOOM_IN = 'top_left_zoom_in';
    case CENTER_ZOOM_OUT = 'center_zoom_out';
    case CENTER_ZOOM_IN = 'center_zoom_in';

    public static function randomUnique(bool $reset = false): static
    {
        static $used = [];

        if ($reset) {
            // Consecutive method calls within test cases quickly use up all the options
            $used = [];
        }

        $cases = self::cases();

        if (count($used) === count($cases)) {
            $used = [];
        }

        $used[] = $random = Arr::random(
            array_filter($cases, fn ($case) => ! in_array($case, $used))
        );

        return $random;
    }
}
