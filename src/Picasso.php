<?php

namespace Laravelista\Picasso;

class Picasso
{
    public function __construct(
        protected Engine $engine,
        protected Storage $storage,
        protected Dimensions $dimensions,
        protected Manifest $manifest
    ) {
    }

    /**
     * Use this to set the input disk dynamically.
     */
    public function setStorageDisk(string $disk): void
    {
        $this->storage->disk(disk: $disk);
    }

    public function optimize(
        string|array $image,
        string|array $dimension,
        ?string $disk = null
    ): void {
        if (is_array($image)) {
            for ($i = 0; $i < count($image); $i++) {
                $this->optimize(
                    image: $image[$i],
                    dimension: $dimension,
                    disk: $disk
                );
            }

            return;
        }

        $this->dimensions->hasOrFail(dimension: $dimension);

        if (is_array($dimension)) {
            // Create one image per dimension.
            for ($i = 0; $i < count($dimension); $i++) {
                $this->process(
                    image: $image,
                    dimension: $dimension[$i],
                    disk: $disk
                );
            }
        } else {
            $this->process(
                image: $image,
                dimension: $dimension,
                disk: $disk
            );
        }
    }

    protected function process(
        string $image,
        string $dimension,
        ?string $disk = null
    ): void {
        // Get image from storage for manipulation.
        $raw_image = $this->storage->get(path: $image);

        $optimized_image = $this->engine->manipulate(
            image: $raw_image,
            dimension: $dimension
        );

        $optimized_image_name = $this->getOptimizedImageName(
            image: $image,
            dimension: $dimension
        );

        // Save to storage.
        $this->storage->put(
            name: $optimized_image_name,
            content: $optimized_image,
            disk: $disk
        );

        // Update manifest of optimized images.
        $this->manifest->update(
            image: $image,
            dimension: $dimension,
            optimized_image: $optimized_image_name,
            format: $this->engine->getFormat(dimension: $dimension),
            quality: $this->engine->getQuality(dimension: $dimension)
        );
    }

    protected function getOptimizedImageName(
        string $image,
        string $dimension
    ): string {
        return "$image-$dimension.{$this->engine->getFormat(dimension: $dimension)}";
    }

    /**
     * It returns the optimized if for given dimension if any, or
     * it returns the original image and logs the missing optimized
     * image to the log file.
     */
    public function get(
        string $image,
        string $dimension,
        ?string $disk = null
    ): string {
        $this->dimensions->hasOrFail(dimension: $dimension);

        $record = $this->manifest->get(
            image: $image,
            dimension: $dimension
        );

        if (is_null($record)) {
            throw new \Exception(
                "Image not found! Image: {$image} Dimension: {$dimension} Disk: {$disk}"
            );
        }

        return $this->storage->url(
            path: $record['src'],
            disk: $disk
        );
    }

    public function drop(
        string $image,
        string|array $dimension,
        ?string $disk = null
    ): void {
        if (is_array($dimension)) {
            for ($i = 0; $i < count($dimension); $i++) {
                $this->drop(
                    image: $image,
                    dimension: $dimension[$i],
                    disk: $disk
                );
            }

            return;
        }

        $record = $this->manifest->get(
            image: $image,
            dimension: $dimension
        );

        if (is_null($record)) {
            throw new \Exception(
                "Record not found! Image: {$image} Dimension: {$dimension} Disk: {$disk}"
            );
        }

        $this->storage->delete(
            path: $record['src'],
            disk: $disk
        );

        $this->manifest->delete(
            image: $image,
            dimension: $dimension
        );
    }

    public function purge(string $image, ?string $disk = null): void
    {
        $records = $this->manifest->get(image: $image) ?? [];

        foreach ($records as $dimension => $record) {
            $this->storage->delete(
                path: $record['src'],
                disk: $disk
            );

            $this->manifest->delete(
                image: $image,
                dimension: $dimension
            );
        }
    }
}
