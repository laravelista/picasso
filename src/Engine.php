<?php

namespace Laravelista\Picasso;

use Illuminate\Support\Facades\Config;
use Intervention\Image\ImageManager;

class Engine
{
    protected ImageManager $manager;
    protected Dimensions $dimensions;

    protected int $quality;
    protected string $format;

    public function __construct(ImageManager $manager, Dimensions $dimensions)
    {
        $this->manager = $manager;
        $this->dimensions = $dimensions;

        $this->quality = Config::get('picasso.quality');
        $this->format = Config::get('picasso.format');
    }

    public function setQuality(int $quality): void
    {
        $this->quality = $quality;
    }

    public function getQuality(string $dimension): int
    {
        return $this->dimensions->getQuality($dimension) ?? $this->quality;
    }

    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    public function getFormat(string $dimension): string
    {
        return $this->dimensions->getFormat($dimension) ?? $this->format;
    }

    /**
     * @param string $image
     * @param string $dimension
     * @return string
     */
    public function manipulate(string $image, string $dimension): string
    {
        return $this->manager
            ->make($image)
            ->fit(
                $this->dimensions->getWidth($dimension),
                $this->dimensions->getHeight($dimension)
            )
            ->encode(
                $this->getFormat($dimension),
                $this->getQuality($dimension)
            )
            ->__toString();
    }
}
