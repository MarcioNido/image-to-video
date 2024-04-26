<?php

namespace App\Console\Commands;

use App\Services\FfmpegService;
use App\Services\FrameService\Animations\StaticImage;
use App\Services\FrameService\FrameService;
use App\Services\FrameService\Transitions\OpaqueTransition;
use App\Services\ImageService\ImageService;
use Illuminate\Console\Command;
use ImagickException;

class ImageToVideo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:image-to-video';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(
        protected FfmpegService $ffmpeg,
        protected FrameService $frameService,
        protected ImageService $imageService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @throws ImagickException
     */
    public function handle(): void
    {
        $images = $this->imageService->prepareImages([
            'storage/image-files/1.jpg',
            'storage/image-files/2.jpg',
            'storage/image-files/3.jpg',
            'storage/image-files/4.jpg',
            'storage/image-files/5.jpg',
        ], ImageService::IMAGE_SIZE_1024X576);

        for ($i=0; $i<count($images); $i++) {
            $this->frameService->animate($images[$i], new StaticImage(), 2);
            if ($i < count($images) - 1) {
                $this->frameService->transition($images[$i], $images[$i + 1], new OpaqueTransition(), 0.5);
            }
        }

        $frames = $this->frameService->getFrames();

        $this->ffmpeg->createVideo($frames, storage_path('video-files/video_' . uniqid() . '.mov'));

    }
}
