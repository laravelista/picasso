<?php

namespace Laravelista\Picasso;

use Illuminate\Support\Arr;

class Dimensions
{
    protected $collection;

    public function __construct()
    {
        $this->collection = config('picasso.dimensions');
    }

    /**
     * Check if the dimension exists in the config.
     *
     * @param string|array $dimension
     * @throws \Exception
     * @return null
     */
    public function hasOrFail($dimension)
    {
        if (!Arr::has($this->collection, $dimension)) {
            throw new \Exception('Dimension not valid. Check `config/picasso.php` for supported dimensions.');
        }
    }

    public function getWidth($dimension)
    {
        return Arr::get($this->collection, $dimension . '.width');
    }

    public function getHeight($dimension)
    {
        return Arr::get($this->collection, $dimension . '.height');
    }

    public function getQuality($dimension)
    {
        return Arr::get($this->collection, $dimension . '.quality');
    }

    public function getFormat($dimension)
    {
        return Arr::get($this->collection, $dimension . '.format');
    }

}
