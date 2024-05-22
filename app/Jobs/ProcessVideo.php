<?php

namespace App\Jobs;

use App\Enums\AnimationTypeEnum;
use App\Enums\TransitionTypeEnum;
use App\Enums\VideoSizeEnum;
use App\Models\Video;
use App\Services\FfmpegService;
use App\Services\ImageService\ImageService;
use App\Services\SequenceService\SequenceService;
use App\Services\WebhookService\WebhookService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        ImageService $imageService,
        SequenceService $sequenceService,
        WebhookService $webhookService
    ): void {

        $this->video->update([
            'status' => Video::STATUS_PROCESSING,
        ]);

        $webhookService->sendWebHook(
            $this->video->webhook,
            [
                'id' => $this->video->id,
                'status' => Video::STATUS_PROCESSING,
            ]
        );


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

        // add texts
        $texts = $this->video->texts;

        if ($texts && count($texts) > 0) {
            foreach ($texts as $text) {
                $sequenceService->addTextSequence(
                    text: $text['title'],
                    x: $text['x'],
                    y: $text['y'],
                    startTime: $text['start_time'],
                    duration: $text['duration']
                );
            }
        }

        $sequenceService->processSequence();

        try {

            $ffmpegService->createVideo(
                frames: $sequenceService->getFrames(),
                outputFile: Storage::disk('workspace')->path($this->video->id . '/video.mp4'),
            );

            $ffmpegService->addAudio(
                videoFile: Storage::disk('workspace')->path($this->video->id . '/video.mp4'),
                audioFile: Storage::disk('audio-files')->path($this->video->soundtrack->path),
                outputFile: Storage::disk('video-files')->path($this->video->id . '.mp4'),
            );

        } catch (Exception $e) {
            $this->video->update([
                'status' => Video::STATUS_FAILED,
            ]);

            $webhookService->sendWebHook(
                $this->video->webhook,
                [
                    'id' => $this->video->id,
                    'status' => Video::STATUS_FAILED,
                ]
            );

            $this->fail($e);
        }

        $this->video->update([
            'status' => Video::STATUS_READY,
        ]);

        $webhookService->sendWebHook(
            $this->video->webhook,
            [
                'id' => $this->video->id,
                'url' => Storage::disk('video-files')->url($this->video->id . '.mp4'), // 'http://localhost:8000/storage/video-files/' . $this->video->id . '.mp4
                'status' => Video::STATUS_READY,
            ]
        );

        Storage::disk('workspace')->deleteDirectory($this->video->id);
    }
}
