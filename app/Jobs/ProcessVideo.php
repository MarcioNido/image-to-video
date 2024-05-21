<?php

namespace App\Jobs;

use App\Enums\AnimationTypeEnum;
use App\Enums\TransitionTypeEnum;
use App\Enums\VideoSizeEnum;
use App\Models\Video;
use App\Services\FfmpegService;
use App\Services\FrameService\FrameService;
use App\Services\ImageService\ImageService;
use App\Services\SequenceService\SequenceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const ANIMATIONS = [
        AnimationTypeEnum::TOP_LEFT_TO_BOTTOM_RIGHT,
        AnimationTypeEnum::TOP_RIGHT_TO_BOTTOM_LEFT,
        AnimationTypeEnum::BOTTOM_LEFT_TO_TOP_RIGHT,
        AnimationTypeEnum::BOTTOM_RIGHT_TO_TOP_LEFT,
        AnimationTypeEnum::TOP_LEFT_ZOOM_OUT,
        AnimationTypeEnum::TOP_LEFT_ZOOM_IN,
        AnimationTypeEnum::CENTER_ZOOM_OUT,
        AnimationTypeEnum::CENTER_ZOOM_IN,
    ];

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Video $video
    ) {}

    /**
     * Execute the job.
     */
    public function handle(
        FfmpegService $ffmpegService,
        FrameService $frameService,
        ImageService $imageService,
        SequenceService $sequenceService
    ): void {
        $images = $imageService->prepareImages(
            $this->video->images->pluck('path')->toArray(),
            VideoSizeEnum::LARGE
        );

        $sequenceService->setVideoSize(VideoSizeEnum::LARGE);

        foreach ($images as $image) {
            $sequenceService->addImageSequence(
                image: $image,
                animationType: AnimationTypeEnum::randomUnique(),
                seconds: 5,
                transitionType: TransitionTypeEnum::MERGE,
                transitionSeconds: 1
            );
        }

        $sequenceService->processSequence();

        try {
            $ffmpegService->createVideo(
                $sequenceService->getFrames(),
                storage_path('video-files/' . $this->video->id . '.mp4'),
            );
        } catch (\Exception $e) {
            $this->video->update([
                'status' => Video::STATUS_FAILED,
            ]);
            $this->fail($e);
        }

        $this->video->update([
            'status' => Video::STATUS_READY,
        ]);
    }
}
