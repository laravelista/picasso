<?php

namespace Laravelista\Picasso;

use Illuminate\Support\Facades\Config;
use Intervention\Image\ImageManager;

class Engine
{
    protected int $quality;

    protected string $format;

    public function __construct(protected ImageManager $manager, protected Dimensions $dimensions)
    {
        $this->quality = Config::get('picasso.quality');
        $this->format = Config::get('picasso.format');
    }

    public function setQuality(int $quality): void
    {
        $this->quality = $quality;
    }

    public function getQuality(string $dimension): int
    {
        return $this->dimensions->getQuality(dimension: $dimension) ?? $this->quality;
    }

    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    public function getFormat(string $dimension): string
    {
        return $this->dimensions->getFormat(dimension: $dimension) ?? $this->format;
    }

    public function manipulate(string $image, string $dimension): string
    {
        return $this->manager
            ->make(data: $image)
            ->fit(
                width: $this->dimensions->getWidth(dimension: $dimension),
                height: $this->dimensions->getHeight(dimension: $dimension)
            )
            ->encode(
                format: $this->getFormat(dimension: $dimension),
                quality: $this->getQuality(dimension: $dimension)
            )
            ->__toString();
    }
}
