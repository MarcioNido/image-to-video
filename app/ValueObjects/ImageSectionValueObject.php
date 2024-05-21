<?php

namespace App\ValueObjects;

use App\Enums\AnimationTypeEnum;
use App\Enums\TransitionTypeEnum;
use App\Enums\VideoSizeEnum;
use App\Services\FrameService\FrameService;
use App\Services\ImageService\ImageService;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Interfaces\ImageInterface;

class ImageSectionValueObject
{
    public function __construct(
        protected ImageInterface $image,
        protected AnimationTypeEnum $animationType,
        protected float $seconds,
        protected ?TransitionTypeEnum $transitionType = null,
        protected ?float $transitionSeconds = null
    ) {}

    public function getImage(): ImageInterface
    {
        return $this->image;
    }

    public function getAnimationType(): AnimationTypeEnum
    {
        return $this->animationType;
    }

    public function getSeconds(): int
    {
        return $this->seconds;
    }

    public function getTransitionType(): ?TransitionTypeEnum
    {
        return $this->transitionType;
    }

    public function getTransitionSeconds(): ?int
    {
        return $this->transitionSeconds;
    }

    public function getImageAtFramePosition(int $framePosition, VideoSizeEnum $videoSize): ImageInterface
    {
        $image = clone $this->image;

        [$width, $height, $left, $top] = $this->getLeftTopValues($videoSize, $framePosition);

        if ($width && $height) {
            $image->resize($width, $height);
        }

        $image->crop($videoSize->getWidth(), $videoSize->getHeight(), $left, $top);
        return $image;
    }

    protected function getLeftTopValues(VideoSizeEnum $videoSize, int $framePosition): array
    {
        $width = null;
        $height = null;

        match ($this->animationType) {
            AnimationTypeEnum::TOP_LEFT_TO_BOTTOM_RIGHT => [$left, $top] = $this->getLeftTopValuesForTopLeftToBottomRight($videoSize, $framePosition),
            AnimationTypeEnum::TOP_RIGHT_TO_BOTTOM_LEFT => [$left, $top] = $this->getLefTopValuesForTopRightToBottomLeft($videoSize, $framePosition),
            AnimationTypeEnum::BOTTOM_LEFT_TO_TOP_RIGHT => [$left, $top] = $this->getLeftTopValuesForBottomLeftToTopRight($videoSize, $framePosition),
            AnimationTypeEnum::BOTTOM_RIGHT_TO_TOP_LEFT => [$left, $top] = $this->getLefTopValuesForBottomRightToTopLeft($videoSize, $framePosition),
            AnimationTypeEnum::TOP_LEFT_ZOOM_OUT => [$width, $height, $left, $top] = $this->getValuesForTopLeftZoomOut($videoSize, $framePosition),
            AnimationTypeEnum::TOP_LEFT_ZOOM_IN => [$width, $height, $left, $top] = $this->getValuesForTopLeftZoomIn($videoSize, $framePosition),
            AnimationTypeEnum::CENTER_ZOOM_OUT => [$width, $height, $left, $top] = $this->getValuesForCenterZoomOut($videoSize, $framePosition),
            AnimationTypeEnum::CENTER_ZOOM_IN => [$width, $height, $left, $top] = $this->getValuesForCenterZoomIn($videoSize, $framePosition),
            default => [$left, $top] = $this->getLeftTopValuesForStatic($videoSize),
        };
        return [$width, $height, $left, $top];
    }

    /**
     * For static, crop the image to the video size in the center position
     */
    private function getLeftTopValuesForStatic(VideoSizeEnum $videoSize): array
    {
        $left = ($this->image->width() - ($videoSize->getWidth() * ImageService::OVERSIZE_FACTOR)) / 2;
        $top = ($this->image->height() - ($videoSize->getHeight() * ImageService::OVERSIZE_FACTOR)) / 2;
        return [$left, $top];
    }

    /**
     * Based on to total of frames, calculate the left and top values for the image at the requested frame
     */
    private function getLeftTopValuesForTopLeftToBottomRight(VideoSizeEnum $videoSize, int $framePosition): array
    {
        $totalFrames = $this->seconds * FrameService::FRAMES_PER_SECOND;
        $deltaX = $this->image->width() - ($videoSize->getWidth() * ImageService::OVERSIZE_FACTOR);
        $deltaY = $this->image->height() - ($videoSize->getHeight() * ImageService::OVERSIZE_FACTOR);
        $factor = 100 / $totalFrames;
        $left = $deltaX * ($factor * $framePosition) / 100;
        $top = $deltaY * ($factor * $framePosition) / 100;

        return [$left, $top];
    }

    private function getLefTopValuesForTopRightToBottomLeft(VideoSizeEnum $videoSize, int $framePosition): array
    {
        $totalFrames = $this->seconds * FrameService::FRAMES_PER_SECOND;
        $deltaX = $this->image->width() - ($videoSize->getWidth() * ImageService::OVERSIZE_FACTOR);
        $deltaY = $this->image->height() - ($videoSize->getHeight() * ImageService::OVERSIZE_FACTOR);
        $factor = 100 / $totalFrames;
        $left = $deltaX - $deltaX * ($factor * $framePosition) / 100;
        $top = $deltaY * ($factor * $framePosition) / 100;

        return [$left, $top];
    }

    private function getValuesForTopLeftZoomOut(VideoSizeEnum $videoSize, int $framePosition): array
    {
        $totalFrames = $this->seconds * FrameService::FRAMES_PER_SECOND;
        $deltaX = $this->image->width() - ($videoSize->getWidth() * ImageService::OVERSIZE_FACTOR);
        $deltaY = $this->image->height() - ($videoSize->getHeight() * ImageService::OVERSIZE_FACTOR);
        $factor = 100 / $totalFrames;
        $width = $this->image->width() - $deltaX * ($factor * $framePosition) / 100;
        $height = $this->image->height() - $deltaY * ($factor * $framePosition) / 100;
        $left = 0; // ($this->image->width() - $width) / 2;
        $top = 0; // ($this->image->height() - $height) / 2;

        Log::info('Center Zoom Out', [$width, $height, $left, $top]);

        return [$width, $height, $left, $top];
    }

    private function getValuesForTopLeftZoomIn(VideoSizeEnum $videoSize, int $framePosition): array
    {
        $totalFrames = $this->seconds * FrameService::FRAMES_PER_SECOND;
        $deltaX = $this->image->width() - ($videoSize->getWidth() * ImageService::OVERSIZE_FACTOR);
        $deltaY = $this->image->height() - ($videoSize->getHeight() * ImageService::OVERSIZE_FACTOR);
        $factor = 100 / $totalFrames;
        $width = $videoSize->getWidth() + $deltaX * ($factor * $framePosition) / 100;
        $height = $videoSize->getHeight() + $deltaY * ($factor * $framePosition) / 100;
        $left = 0; //($this->image->width() - $width) / 2;
        $top = 0; // ($this->image->height() - $height) / 2;

        Log::info('Center Zoom In', [$width, $height, $left, $top]);

        return [$width, $height, $left, $top];
    }

    /**
     * Based on to total of frames, calculate the left and top values for the image at the requested frame.
     * The image will move from the bottom left position to the top right position
     */
    private function getLeftTopValuesForBottomLeftToTopRight(VideoSizeEnum $videoSize, int $framePosition): array
    {
        $totalFrames = $this->seconds * FrameService::FRAMES_PER_SECOND;
        $deltaX = $this->image->width() - ($videoSize->getWidth() * ImageService::OVERSIZE_FACTOR);
        $deltaY = $this->image->height() - ($videoSize->getHeight() * ImageService::OVERSIZE_FACTOR);
        $factor = 100 / $totalFrames;
        $left = $deltaX * ($factor * $framePosition) / 100;
        $top = $deltaY - $deltaY * ($factor * $framePosition) / 100;

        return [$left, $top];
    }

    private function getLefTopValuesForBottomRightToTopLeft(VideoSizeEnum $videoSize, int $framePosition): array
    {
        $totalFrames = $this->seconds * FrameService::FRAMES_PER_SECOND;
        $deltaX = $this->image->width() - ($videoSize->getWidth() * ImageService::OVERSIZE_FACTOR);
        $deltaY = $this->image->height() - ($videoSize->getHeight() * ImageService::OVERSIZE_FACTOR);
        $factor = 100 / $totalFrames;
        $left = $deltaX - $deltaX * ($factor * $framePosition) / 100;
        $top = $deltaY - $deltaY * ($factor * $framePosition) / 100;

        return [$left, $top];
    }

    private function getValuesForCenterZoomOut(VideoSizeEnum $videoSize, int $framePosition)
    {
        $totalFrames = $this->seconds * FrameService::FRAMES_PER_SECOND;
        $deltaX = $this->image->width() - ($videoSize->getWidth() * ImageService::OVERSIZE_FACTOR);
        $deltaY = $this->image->height() - ($videoSize->getHeight() * ImageService::OVERSIZE_FACTOR);
        $factor = 100 / $totalFrames;
        $width = $this->image->width() - $deltaX * ($factor * $framePosition) / 100;
        $height = $this->image->height() - $deltaY * ($factor * $framePosition) / 100;
        $left = ($width - $videoSize->getWidth()) / 2;
        $top = ($height - $videoSize->getHeight()) / 2;

        Log::info('Center Zoom Out', [$width, $height, $left, $top]);

        return [$width, $height, $left, $top];
    }

    private function getValuesForCenterZoomIn(VideoSizeEnum $videoSize, int $framePosition)
    {
        $totalFrames = $this->seconds * FrameService::FRAMES_PER_SECOND;
        $deltaX = $this->image->width() - ($videoSize->getWidth() * ImageService::OVERSIZE_FACTOR);
        $deltaY = $this->image->height() - ($videoSize->getHeight() * ImageService::OVERSIZE_FACTOR);
        $factor = 100 / $totalFrames;
        $width = $videoSize->getWidth() + $deltaX * ($factor * $framePosition) / 100;
        $height = $videoSize->getHeight() + $deltaY * ($factor * $framePosition) / 100;
        $left = ($width - $videoSize->getWidth()) / 2;
        $top = ($height - $videoSize->getHeight()) / 2;

        Log::info('Center Zoom In', [$width, $height, $left, $top]);

        return [$width, $height, $left, $top];
    }
}
