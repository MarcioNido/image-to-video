<?php

namespace App\Services\SequenceService;

use App\Enums\AnimationTypeEnum;
use App\Enums\TransitionTypeEnum;
use App\Enums\VideoSizeEnum;
use App\Factories\AnimationFactory;
use App\Factories\TransitionFactory;
use App\Services\FrameService\FrameService;
use App\Services\ImageService\ImageService;
use App\ValueObjects\ImageSectionValueObject;
use App\ValueObjects\TextSectionValueObject;
use Intervention\Image\Interfaces\ImageInterface;

class SequenceService
{
    /** @var array<ImageSectionValueObject> $section */
    protected array $section = [];

    /** @var array<TextSectionValueObject> $textSection */
    protected array $textSection = [];

    /** @var array<ImageInterface> $frames */
    protected array $frames = [];

    protected VideoSizeEnum $videoSize;

    public function __construct(
        protected ImageService $imageService,
        protected FrameService $frameService,
        protected AnimationFactory $animationFactory,
        protected TransitionFactory $transitionFactory
    ) {
    }

    public function addImageSequence(
        ImageInterface $image,
        AnimationTypeEnum $animationType,
        float $seconds,
        ?TransitionTypeEnum $transitionType = null,
        ?float $transitionSeconds = null
    ): static {
        $this->section[] = new ImageSectionValueObject($image, $animationType, $seconds, $transitionType, $transitionSeconds);
        return $this;
    }

    public function addTextSequence(string|null $text, int $x, int $y, int $startTime, int $duration): static
    {
        if (!$text) {
            return $this;
        }

        $this->textSection[] = new TextSectionValueObject($text, $x, $y, $startTime, $duration);
        return $this;
    }

    public function processSequence(): SequenceService
    {
        foreach ($this->section as $key => $section) {

            if ($section->getTransitionType() && $section->getTransitionSeconds()) {
                $this->frameService->transition($section, $this->videoSize);
            }
            $this->frameService->animate($section, $this->videoSize);
        }

        $this->addTextToFrames();

        $this->frames = $this->frameService->getFrames();

        return $this;
    }

    public function getFrames(): array
    {
        return $this->frames;
    }

    public function setVideoSize(VideoSizeEnum $LARGE): static
    {
        $this->videoSize = $LARGE;
        return $this;
    }

    private function addTextToFrames()
    {
        foreach ($this->textSection as $textSection) {

            $firstFrame = $textSection->getStartTime() * FrameService::FRAMES_PER_SECOND;
            $lastFrame = $firstFrame + $textSection->getDuration() * FrameService::FRAMES_PER_SECOND;
            for ($framePosition = $firstFrame; $framePosition < $lastFrame; $framePosition++) {
                $this->frameService->addText(
                    $textSection->getText(),
                    $textSection->getX(),
                    $textSection->getY(),
                    $framePosition
                );
            }
        }
        return $this;
    }
}
