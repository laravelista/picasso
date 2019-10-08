<?php

namespace Laravelista\Picasso;

use Intervention\Image\ImageManager;

class Engine
{
    protected $manager;
    protected $dimensions;

    protected $quality;
    protected $format;

    public function __construct(ImageManager $manager, Dimensions $dimensions)
    {
        $this->manager = $manager;
        $this->dimensions = $dimensions;

        $this->quality = config('picasso.quality');
        $this->format = config('picasso.format');
    }

    public function setQuality(int $quality)
    {
        $this->quality = $quality;
    }

    public function getQuality(string $dimension)
    {
        return $this->dimensions->getQuality($dimension) ?? $this->quality;
    }

    public function setFormat(string $format)
    {
        $this->format = $format;
    }

    public function getFormat(string $dimension)
    {
        return $this->dimensions->getFormat($dimension) ?? $this->format;
    }

    /**
     * @param string $image
     * @param string $dimension
     * @return string
     */
    public function manipulate(string $image, string $dimension)
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