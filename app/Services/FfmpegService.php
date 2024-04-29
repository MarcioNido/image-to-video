<?php

namespace App\Services;

use App\Services\FrameService\FrameService;
use Intervention\Image\Interfaces\ImageInterface;

/**
 * Class FfmpegService
 * ffmpeg -f image2 -r 1 -s 1024X576 -i /var/www/html/storage/video-files/%d.jpg -r 25 -vcodec libx264 -crf 25  -pix_fmt yuv420p /var/www/html/storage/video-files/video.mp4
 */
class FfmpegService
{
    public function createVideo(array $frames, string $outputFile): void
    {
        // create temporary directory and save frames
        $tempDir = storage_path('image-files/' . uniqid());
        mkdir($tempDir);

        foreach ($frames as $key => $frame) {
            /** @var ImageInterface $frame */
            $frame->toPng()->save($tempDir . '/' . $key . '.png');
        }

        // create video from frames using ffmpeg binary
        $framesPerSecond = FrameService::FRAMES_PER_SECOND;
        exec("ffmpeg -f image2 -r $framesPerSecond -s 640x360 -i $tempDir/%d.png -r $framesPerSecond -vcodec libx264 -crf $framesPerSecond  -pix_fmt yuv420p $outputFile");
//        exec("ffmpeg -f image2 -r {$framesPerSecond} -s 1024X576 -i $tempDir/%d.png -r {$framesPerSecond} -c:v prores -pix_fmt yuva444p10le {$outputFile}");
    }
}
