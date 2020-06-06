<?php

namespace Laravelista\Picasso;

class Picasso
{
    protected $engine;
    protected $storage;
    protected $dimensions;
    protected $manifest;

    public function __construct(Engine $engine, Storage $storage, Dimensions $dimensions, Manifest $manifest)
    {
        $this->engine = $engine;
        $this->storage = $storage;
        $this->dimensions = $dimensions;
        $this->manifest = $manifest;
    }

    /**
     * Use this to set the input disk dynamically.
     */
    public function setStorageDisk(string $disk)
    {
        $this->storage->setDisk($disk);
    }

    /**
     * @param string|array $image
     * @param string|array $dimension
     * @param string|null $disk
     * @return null
     */
    public function optimize($image, $dimension, string $disk = null)
    {
        if (is_array($image)) {

            for ($i = 0; $i < count($image); $i++) {
                $this->optimize($image[$i], $dimension, $disk);
            }

            return;
        }

        $this->dimensions->hasOrFail($dimension);

        if (is_array($dimension)) {
            // Create one image per dimension.
            for ($i = 0; $i < count($dimension); $i++) {
                $this->process($image, $dimension[$i], $disk);
            }
        } else {
            $this->process($image, $dimension, $disk);
        }
    }

    protected function process(string $image, string $dimension, $disk = null)
    {
        // Get image from storage for manipulation.
        $raw_image = $this->storage->get($image);

        $optimized_image = $this->engine->manipulate($raw_image, $dimension);

        $optimized_image_name = $this->getOptimizedImageName($image, $dimension);

        // Save to storage.
        $this->storage->put($optimized_image_name, $optimized_image, $disk);

        // Update manifest of optimized images.
        $this->manifest->update(
            $image, $dimension, $optimized_image_name,
            $this->engine->getFormat($dimension),
            $this->engine->getQuality($dimension)
        );
    }

    /**
     * @param string $image
     * @param string $dimension
     * @return string
     */
    protected function getOptimizedImageName(string $image, string $dimension): string
    {
        return "$image-$dimension.{$this->engine->getFormat($dimension)}";
    }

    /**
     * It returns the optimized if for given dimension if any, or
     * it returns the original image and logs the missing optimized
     * image to the log file.
     *
     * @param string $image
     * @param string $dimension
     * @return string
     */
    public function get(string $image, string $dimension, string $disk = null)
    {
        $this->dimensions->hasOrFail($dimension);

        $record = $this->manifest->get($image, $dimension);

        if (is_null($record)) {
            throw new \Exception("Image not found! Image: {$image} Dimension: {$dimension} Disk: {$disk}");
        }

        return $this->storage->url($record['src'], $disk);
    }

    /**
     * @param string $image
     * @param string|array $dimension
     * @param string|null $disk
     */
    public function drop(string $image, $dimension, string $disk = null)
    {
        if (is_array($dimension)) {

            for ($i = 0; $i < count($dimension); $i++) {
                $this->drop($image, $dimension[$i], $disk);
            }

            return;
        }

        $record = $this->manifest->get($image, $dimension);

        if (is_null($record)) {
            throw new \Exception("Record not found! Image: {$image} Dimension: {$dimension} Disk: {$disk}");
        }

        $this->storage->delete($record['src'], $disk);

        $this->manifest->delete($image, $dimension);
    }

    /**
     * @param string $image
     * @param string|null $disk
     */
    public function purge(string $image, string $disk = null)
    {
        $records = $this->manifest->get($image) ?? [];

        foreach ($records as $dimension => $record) {
            $this->storage->delete($record['src'], $disk);

            $this->manifest->delete($image, $dimension);
        }
    }
}