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
    /**
     * Old command, in case we need it:
     * exec("ffmpeg -f image2 -r {$framesPerSecond} -s 1024X576 -i $tempDir/%d.png -r {$framesPerSecond} -c:v prores -pix_fmt yuva444p10le {$outputFile}");
     */
    public function createVideo(array $frames, string $outputFile): void
    {
        $tempDir = storage_path('image-files/' . uniqid());
        mkdir($tempDir);

        foreach ($frames as $key => $frame) {
            /** @var ImageInterface $frame */
            $frame->toPng()->save($tempDir . '/' . $key . '.png');
        }

        $framesPerSecond = FrameService::FRAMES_PER_SECOND;
        /** @todo: change hardcoded video dimensions */
        exec("ffmpeg -f image2 -r $framesPerSecond -s 640x360 -i $tempDir/%d.png -r $framesPerSecond -vcodec libx264 -crf $framesPerSecond  -pix_fmt yuv420p $outputFile");
    }

    /**
     * Old command, in case we need it:
     * exec('ffmpeg -i ' . $this->baseDir . '/' . $this->id . '.mp4' . ' -i ../audios/' . $audioFile . ' -shortest ' . $this->baseDir . '/' . $this->id . 'a.mp4');
     */
    public function addAudio(string $videoFile, string $audioFile, string $outputFile): void
    {
        exec("ffmpeg -i $videoFile -i $audioFile -c:v copy -c:a aac -shortest -strict experimental $outputFile");
    }
}
