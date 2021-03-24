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
        $this->disk = Config::get(key: 'picasso.disk');
    }

    /**
     * It sets the disk.
     */
    public function disk(string $disk): void
    {
        $this->disk = $disk;
    }

    /**
     * It gets the image content from the disk
     * where the originals are located.
     */
    public function get(string $path): string
    {
        return IlluminateStorage::disk(name: $this->disk)->get(path: $path);
    }

    /**
     * It saves the image to the specified disk or
     * to the default application disk.
     */
    public function put(string $name, string $content, ?string $disk = null): void
    {
        IlluminateStorage::disk(name: $disk)->put(
            path: $name,
            contents: $content
        );
    }

    public function url(string $path, ?string $disk = null): string
    {
        return IlluminateStorage::disk(name: $disk)->url($path);
    }

    public function delete(string $path, ?string $disk = null): void
    {
        IlluminateStorage::disk(name: $disk)->delete(paths: $path);
    }
}
