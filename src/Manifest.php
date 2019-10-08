<?php

namespace Laravelista\Picasso;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage as IlluminateStorage;

class Manifest
{
    protected $manifest = [];

    const FILENAME = 'picasso-manifest.json';

    public function __construct(Dimensions $dimensions)
    {
        $this->dimensions = $dimensions;

        $this->manifest = $this->load();
    }

    protected function load(): array
    {
        if (IlluminateStorage::disk('local')->has(self::FILENAME)) {
            return json_decode(IlluminateStorage::disk('local')->get(self::FILENAME), true);
        }

        return [];
    }

    protected function save()
    {
        IlluminateStorage::disk('local')->put(self::FILENAME, json_encode($this->manifest));
    }

    public function update(string $image, string $dimension, string $optimized_image, string $format, int $quality)
    {
        $this->manifest[$image][$dimension] = [
            "src" => $optimized_image,
            "format" => $format,
            "quality" => $quality,
            "width" => $this->dimensions->getWidth($dimension),
            "height" => $this->dimensions->getHeight($dimension)
        ];

        $this->save();
    }

    public function getRecord($image, $dimension)
    {
        return Arr::get($this->manifest, "{$image}.{$dimension}");
    }
}