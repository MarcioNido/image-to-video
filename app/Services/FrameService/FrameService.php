<?php

namespace App\Services\FrameService;

use App\Enums\VideoSizeEnum;
use App\Factories\AnimationFactory;
use App\Factories\TransitionFactory;
use App\Services\ImageService\ImageService;
use App\ValueObjects\ImageSectionValueObject;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Typography\FontFactory;

class FrameService
{
    const FRAMES_PER_SECOND = 10;

    /** @var array<ImageInterface> $frames */
    protected array $frames = [];

    public function __construct(
        protected ImageService $imageService,
        protected AnimationFactory $animationFactory,
        protected TransitionFactory $transitionFactory
    ) {
    }

    public function animate(
        ImageSectionValueObject $imageSection,
        VideoSizeEnum $videoSize
    ): static
    {
        $animation = $this->animationFactory->createAnimation($imageSection->getAnimationType());

        $animationFrames = $animation(
            $imageSection,
            $videoSize
        );

        $this->frames = array_merge($this->frames, $animationFrames);
        return $this;
    }

    public function transition(
        ImageSectionValueObject $imageSection,
        VideoSizeEnum $videoSize
    ): static {
        $transition = $this->transitionFactory->createTransition($imageSection->getTransitionType());

        $transitionFrames = $transition(
            end($this->frames),
            $imageSection,
            $videoSize
        );
        $this->frames = array_merge($this->frames, $transitionFrames);
        return $this;
    }

    public function addText(string $text, int $x, int $y, int $framePosition): static
    {
        $this->frames[$framePosition]->text(
            $text, $x, $y,
            function (FontFactory $font) {
                $font->filename(storage_path('font-files/roboto/Roboto-Regular.ttf'));
                $font->size(30);
                $font->color('22538f');
                $font->stroke('ccc', 3);
                $font->align('left');
                $font->valign('middle');
                $font->lineHeight(1.6);
//                $font->angle(10);
//                $font->wrap(250);
            }
        );
        return $this;
    }

    public function getFrames(): array
    {
        return $this->frames;
    }
}
