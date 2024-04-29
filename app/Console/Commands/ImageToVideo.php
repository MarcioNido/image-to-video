<?php

namespace App\Console\Commands;

use App\Enums\AnimationTypeEnum;
use App\Enums\TransitionTypeEnum;
use App\Enums\VideoSizeEnum;
use App\Services\FfmpegService;
use App\Services\FrameService\FrameService;
use App\Services\ImageService\ImageService;
use App\Services\SequenceService\SequenceService;
use Illuminate\Console\Command;

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
        protected ImageService $imageService,
        protected SequenceService $sequence
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $images = $this->imageService->prepareImages([
            'storage/image-files/I000758509S008050129.jpeg',
            'storage/image-files/I000758509S008050130.jpeg',
            'storage/image-files/I000758509S008050131.jpeg',
            'storage/image-files/I000758509S008050132.jpeg',
            'storage/image-files/I000758509S008050133.jpeg',
        ], VideoSizeEnum::LARGE);

        // think
        $this->sequence
            ->setVideoSize(VideoSizeEnum::MEDIUM)
            ->addImageSequence(
                image: $images[0],
                animationType: AnimationTypeEnum::TOP_LEFT_TO_BOTTOM_RIGHT,
                seconds: 5,
            )->addImageSequence(
                image: $images[1],
                animationType: AnimationTypeEnum::TOP_RIGHT_TO_BOTTOM_LEFT,
                seconds: 5,
                transitionType: TransitionTypeEnum::MERGE,
                transitionSeconds: 1
            )->addImageSequence(
                image: $images[2],
                animationType: AnimationTypeEnum::CENTER_ZOOM_OUT,
                seconds: 5,
                transitionType: TransitionTypeEnum::MERGE,
                transitionSeconds: 1
            )->addImageSequence(
                image: $images[3],
                animationType: AnimationTypeEnum::BOTTOM_RIGHT_TO_TOP_LEFT,
                seconds: 5,
                transitionType: TransitionTypeEnum::MERGE,
                transitionSeconds: 1
            )->addImageSequence(
                image: $images[4],
                animationType: AnimationTypeEnum::CENTER_ZOOM_IN,
                seconds: 5,
                transitionType: TransitionTypeEnum::MERGE,
                transitionSeconds: 1
            )->addTextSequence(
                text: 'Apartamento',
                x: 30,
                y: 250,
                startTime: 3,
                duration: 6
            )
            ->addTextSequence(
                text: 'Vila Mariana - São Paulo - SP',
                x: 30,
                y: 300,
                startTime: 4,
                duration: 5
            )
            ->addTextSequence(
                text: '3 dorms (1 suíte) - 2 vagas',
                x: 30,
                y: 250,
                startTime: 13,
                duration: 6
            )
            ->addTextSequence(
                text: '120m² de área útil - 200m² de área total',
                x: 30,
                y: 300,
                startTime: 14,
                duration: 5
            )
            ->addTextSequence(
                text: 'Não perca essa oportunidade!',
                x: 30,
                y: 300,
                startTime: 20,
                duration: 5
            )
            ->processSequence();


        $frames = $this->sequence->getFrames();

        $this->ffmpeg->createVideo($frames, storage_path('video-files/video_' . uniqid() . '.mp4'));

    }
}
