<?php

namespace Laravelista\Picasso;

use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;

class Picasso
{
    protected $dimensions;
    protected $manager;
    protected $quality;
    protected $format;

    public function __construct(ImageManager $manager)
    {
        $this->manager = $manager;
        $this->dimensions = config('picasso.dimensions');
        $this->quality = config('picasso.quality');
        $this->format = config('picasso.format');
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

        $this->checkDimension($dimension);

        // create one image per dimension
        if (is_array($dimension)) {
            for ($i = 0; $i < count($dimension); $i++) {
                $this->manipulate($image, $dimension[$i], $disk);
            }
        } else {
            $this->manipulate($image, $dimension, $disk);
        }
    }

    /**
     * @param string|array $dimension
     * @throws \Exception
     * @return null
     */
    protected function checkDimension($dimension)
    {
        if (!Arr::has($this->dimensions, $dimension)) {
            throw new \Exception('Dimension not valid. Check `config/picasso.php` for supported dimensions.');
        }
    }

    /**
     * @param string $image
     * @param string|null $disk
     */
    protected function getImageFromStorage($image, $disk = null)
    {
        return Storage::disk($disk)->get($image);
    }

    /**
     * @param string $image
     * @param string $dimension
     * @param string|null $disk
     * @return null
     */
    protected function manipulate(string $image, string $dimension, string $disk = null)
    {
        $optimizedImage = $this->manager
            ->make($this->getImageFromStorage($image, $disk))
            ->fit(
                Arr::get($this->dimensions, $dimension . '.width'),
                Arr::get($this->dimensions, $dimension . '.height')
            )
            ->encode($this->getDimensionFormat($dimension), $this->getDimensionQuality($dimension))
            ->__toString();

        $optimizedImageName = $this->getOptimizedImageName($image, $dimension);

        $this->saveToStorage($optimizedImageName, $optimizedImage, $disk);

        $this->saveToDatabase($image, $dimension, $optimizedImageName);
    }

    /**
     * It returns the format for the dimension or the global format.
     *
     * @param string $dimension
     * @return string
     */
    protected function getDimensionFormat(string $dimension)
    {
        if (Arr::has($this->dimensions, $dimension . 'format')) {
            return Arr::get($this->dimensions, $dimension . 'format');
        }

        return $this->format;
    }

    /**
     * It returns the quality for the dimension or the global quality.
     *
     * @param string $dimension
     * @return integer
     */
    protected function getDimensionQuality(string $dimension)
    {
        if (Arr::has($this->dimensions, $dimension . 'quality')) {
            return Arr::get($this->dimensions, $dimension . 'quality');
        }

        return $this->quality;
    }

    /**
     * @param string $name
     * @param string $content
     * @param string|null $disk
     * @return null
     */
    protected function saveToStorage(string $name, string $content, $disk = null)
    {
        Storage::disk($disk)->put($name, $content);
    }

    /**
     * @param string $image
     * @param string $dimension
     * @return string
     */
    protected function getOptimizedImageName(string $image, string $dimension): string
    {
        return "$image-$dimension.{$this->getDimensionFormat($dimension)}";
    }

    /**
     * @param string $image
     * @param string $dimension
     * @param string $optimizedImage
     * @return null
     */
    protected function saveToDatabase(string $image, string $dimension, string $optimizedImage)
    {
        $imageInDatabase = Image::where('original_image', $image)->where('dimension_name', $dimension)->first();

        if (is_null($imageInDatabase)) {
            Image::create([
                'original_image' => $image,
                'dimension_name' => $dimension,
                'optimized_image' => $optimizedImage
            ]);
        }
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
        $this->checkDimension($dimension);

        $optimizedImage = Image::where('original_image', $image)->where('dimension_name', $dimension)->first();

        // If there is no optimized image found
        // log that there is no optimized image
        // and return the original image
        if (is_null($optimizedImage)) {
            Log::info("Image not optimized: $image");

            return Storage::disk($disk)->url($image);
        }

        return Storage::disk($disk)->url($optimizedImage->optimized_image);
    }

    /**
     * @param string $image
     * @param string|null $disk
     */
    public function purge(string $image, string $disk = null)
    {
        // get optimized images for image
        $images = Image::where('original_image', $image);

        // delete from storage
        foreach ($images->get() as $image) {
            Storage::disk($disk)->delete($image->optimized_image);
        }

        // delete from database
        $images->delete();
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

        // get optimized images for image
        $image = Image::where('original_image', $image)->where('dimension_name', $dimension)->get();

        // delete from storage
        Storage::disk($disk)->delete($image->optimized_image);

        // delete from database
        $image->delete();
    }
}
