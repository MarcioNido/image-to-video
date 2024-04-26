<?php

namespace App\Services;

use Imagick;
use ImagickException;

/**
 * Class FfmpegService
 * ffmpeg -f image2 -r 1 -s 1024X576 -i /var/www/html/storage/video-files/%d.jpg -r 25 -vcodec libx264 -crf 25  -pix_fmt yuv420p /var/www/html/storage/video-files/video.mp4
 */
class FfmpegService
{
    /**
     * @throws ImagickException
     */
    public function createVideo(array $frames, string $outputFile): void
    {
        // create temporary directory and save frames
        $tempDir = storage_path('image-files/' . uniqid());
        mkdir($tempDir);

        foreach ($frames as $key => $frame) {
            /** @var Imagick $frame */
            $frame->writeImage($tempDir . '/' . $key . '.png');
        }

        // create video from frames using ffmpeg binary
        exec("ffmpeg -f image2 -r 25 -s 1024X576 -i $tempDir/%d.png -r 25 -c:v prores -pix_fmt yuva444p10le {$outputFile}");
    }
}
