<?php

namespace Laravelista\Picasso;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage as IlluminateStorage;

class Storage
{
    /**
     * Disk used for getting images.
     * Use `disk` to change the disk.
     */
    protected string $disk;

    public function __construct()
    {
        $this->disk = Config::get('picasso.disk');
    }

    public function disk(string $disk): void
    {
        $this->disk = $disk;
    }

    /**
     * It gets the image content from the disk
     * where the originals are located.
     *
     * @param string $path
     *
     * @return string
     */
    public function get($path): string
    {
        return IlluminateStorage::disk($this->disk)->get($path);
    }

    /**
     * It saves the image to the specified disk or
     * to the default application disk.
     *
     * @param string $name
     * @param string $content
     * @param string|null $disk
     *
     * @return void
     */
    public function put(string $name, string $content, $disk = null): void
    {
        IlluminateStorage::disk($disk)->put($name, $content);
    }

    /**
     * @param string $path
     * @param null|string $disk
     */
    public function url(string $path, ?string $disk = null): string
    {
        return IlluminateStorage::disk($disk)->url($path);
    }

    /**
     * @param string $path
     * @param null|string $disk
     */
    public function delete(string $path, ?string $disk = null): void
    {
        IlluminateStorage::disk($disk)->delete($path);
    }
}
