<?php

namespace Laravelista\Picasso;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

class Dimensions
{
    protected array $collection;

    public function __construct()
    {
        $this->collection = Config::get(key: 'picasso.dimensions');
    }

    /**
     * Check if the dimension exists in the config.
     *
     * @throws \Exception
     */
    public function hasOrFail(string|array $dimension): void
    {
        if (!Arr::has(
            array: $this->collection,
            keys: $dimension
        )) {
            throw new \Exception(
                message: 'Dimension not valid. Check `config/picasso.php` for supported dimensions.'
            );
        }
    }

    public function getWidth(string $dimension): int
    {
        return Arr::get(
            array: $this->collection,
            key: $dimension . '.width'
        );
    }

    public function getHeight(string $dimension): int
    {
        return Arr::get(
            array: $this->collection,
            key: $dimension . '.height'
        );
    }

    public function getQuality(string $dimension): ?int
    {
        return Arr::get(
            array: $this->collection,
            key: $dimension . '.quality'
        );
    }

    public function getFormat(string $dimension): ?string
    {
        return Arr::get(
            array: $this->collection,
            key: $dimension . '.format'
        );
    }
}
