<?php

namespace Laravelista\Picasso;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

class Dimensions
{
    protected array $collection;

    public function __construct()
    {
        $this->collection = Config::get('picasso.dimensions');
    }

    /**
     * Check if the dimension exists in the config.
     *
     * @param string|array $dimension
     *
     * @throws \Exception
     *
     * @return void
     */
    public function hasOrFail($dimension): void
    {
        if (!Arr::has($this->collection, $dimension)) {
            throw new \Exception('Dimension not valid. Check `config/picasso.php` for supported dimensions.');
        }
    }

    public function getWidth(string $dimension): mixed
    {
        return Arr::get($this->collection, $dimension . '.width');
    }

    public function getHeight(string $dimension): mixed
    {
        return Arr::get($this->collection, $dimension . '.height');
    }

    public function getQuality(string $dimension): mixed
    {
        return Arr::get($this->collection, $dimension . '.quality');
    }

    public function getFormat(string $dimension): mixed
    {
        return Arr::get($this->collection, $dimension . '.format');
    }
}
