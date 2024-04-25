<?php

namespace App\Services;

/**
 * Class FfmpegService
 * ffmpeg -f image2 -r 1 -s 1024X576 -i /var/www/html/storage/video-files/%d.jpg -r 25 -vcodec libx264 -crf 25  -pix_fmt yuv420p /var/www/html/storage/video-files/video.mp4
 */
class FfmpegService
{

}
