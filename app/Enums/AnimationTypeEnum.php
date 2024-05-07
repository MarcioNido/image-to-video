<?php

namespace App\Enums;

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
}
