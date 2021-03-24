<?php

namespace Laravelista\Picasso;

use Illuminate\Support\Facades\Storage as IlluminateStorage;

class Manifest
{
    protected array $manifest = [];

    public const FILENAME = 'picasso-manifest.json';

    public function __construct(protected Dimensions $dimensions)
    {
        $this->manifest = $this->load();
    }

    protected function load(): array
    {
        if (IlluminateStorage::disk(name: 'local')->has(self::FILENAME)) {
            return json_decode(
                IlluminateStorage::disk(name: 'local')->get(path: self::FILENAME),
                true
            );
        }

        return [];
    }

    protected function save(): void
    {
        IlluminateStorage::disk(name: 'local')->put(
            path: self::FILENAME,
            contents: json_encode(value: $this->manifest)
        );
    }

    public function update(
        string $image,
        string $dimension,
        string $optimized_image,
        string $format,
        int $quality
    ): void {
        $this->manifest[$image][$dimension] = [
            "src" => $optimized_image,
            "format" => $format,
            "quality" => $quality,
            "width" => $this->dimensions->getWidth(dimension: $dimension),
            "height" => $this->dimensions->getHeight(dimension: $dimension)
        ];

        $this->save();
    }

    public function delete(string $image, string $dimension): void
    {
        unset($this->manifest[$image][$dimension]);

        $this->save();
    }

    public function get(string $image, ?string $dimension = null): ?array
    {
        if (is_null($dimension)) {
            if (!array_key_exists(
                key: $image,
                search: $this->manifest
            )) {
                return null;
            }

            return $this->manifest[$image];
        }

        if (!array_key_exists(
            key: $image,
            search: $this->manifest
        ) or
            !array_key_exists(
                key: $dimension,
                search: $this->manifest[$image]
            )
        ) {
            return null;
        }

        return $this->manifest[$image][$dimension];
    }
}
