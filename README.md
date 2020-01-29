# Picasso

Picasso is a Laravel Image Management and Optimization Package. Define image dimensions and options, store uploaded image in multiple dimensions with or without a watermark and retrieve optimized images on your website when needed.

[![Become a Patron](https://img.shields.io/badge/Becoma%20a-Patron-f96854.svg?style=for-the-badge)](https://www.patreon.com/laravelista)

## Overview

To reduce site size and improve site loading time, this packages enables you to:

- resize image to multiple dimensions; _[width x height with upscale]_ ('news_thumbnail', 'news_gallery', 'news_cover', ...)
- retrieve optimized image for specific position `get('news_cover')`
- apply watermark on images that need it **(task in progress)**
- quickly change the image dimension and update all, subset or a single image with optimized size
- implement this package in any phase of your application
- to use this package on your whole site or just a part of it
- set global or individual image quality (default **60**)
- set global or individual image format (default **webp**)

### How it works

In the config file you define the dimension width and height, (format and quality are optional) and unique name for that dimension. In your controller call `$picasso->optimize('images/image.jpg', ['home_slideshow_large', 'home_slideshow_thumbnail']')`. This method takes the original image, optimizes it according to the entered dimensions, saves it to storage and saves the record to the manifest file

Later, when you call `Picasso::get('images/image.jpg', 'home_slideshow_large')` you will get the optimized image.

### Benefits

You can keep your original user uploaded images untouched (2MB or more). This package will create new optimized images and keep reference of the original and optimized in the manifest file.

Your page will load faster because it will have less MB to download because the images will be smaller. I have managed to reduce image size from 2.4MB to 700Kb, just by implementing this package as an addon later in the development phase.

## Installation

From the command line:

```
composer require laravelista/picasso
```

Publish the config file `picasso.php` to your `/config` directory:

```
php artisan vendor:publish --provider="Laravelista\Picasso\ServiceProvider" --tag=config
```

Installation complete!

## Configuration

Before continuing be sure to open the `/config/picasso.php` file and update the dimensions and quality to your needs.

## Usage

There are a few ways to implement this package in your application. I will try to cover them all.

### Store method

After you have stored the user uploaded image in your storage `UploadedFile $image->store('images')` and you have retrieved the path to the image. Give that path (that you would usually store in the database) to picasso:

```
use Laravelista\Picasso\Picasso;

public function store(Request $request, Picasso $picasso)
{
    // ...

    // store original image in storage
    $article->image = $request->image->store('images');

    // optimize original image to desired dimensions
    $picasso->optimize($article->image, ['news_small', 'news_cover']);

    // ...
}
```

### Update method

When the user is going to replace the existing image with a new one, we have to first purge all records from storage and manifest file of the old image and then optimize the new image:

```
use Laravelista\Picasso\Picasso;

public function update(Request $request, Article $article, Picasso $picasso)
{
    // ...

    if ($request->hasFile('image')) {

        // delete original image from storage
        Storage::delete($article->image);

        // delete all optimized images for old image
        $picasso->drop($article->image, ['news_small', 'news_cover']);

        // save new original image to storage and retrieve the path
        $article->image = $request->image->store('images');

        // optimize new original image
        $picasso->optimize($article->image, ['news_small', 'news_cover']);
    }

    // ...
}
```

### Destroy method

When deleting a record which has optimized images, be sure to delete optimized image also to reduce unused files:

```
use Laravelista\Picasso\Picasso;

public function destroy(Article $article, Picasso $picasso)
{
    // ...

    // delete original image
    Storage::delete($article->image);

    // delete optimized images
    $picasso->purge($article->image);

    // delete record from database
    $article->delete();

    // ...
}
```

### Optimizing already uploaded and saved images

My suggestion is to create a console route for this. I will show you how I do this in my applications. In `routes/console.php` and this route:

```
use Laravelista\Picasso\Picasso;

Artisan::command('picasso:article-optimize', function (Picasso $picasso) {

    $images = Article::all()->pluck('image')->toArray();

    $picasso->optimize($images, ['news_small', 'news_cover']);

    $this->comment("Article images optimized!");
});
```

Now from the command line you can call `php artisan picasso:article-optimize` whenever you want and it will grab the original images for table article, created optimized images, create/update optimized images in storage and update the reference in the database.

### Retrieving optimized images

From your view files do:

```
<image src="{{ Picasso::get($article->image, 'news_small') }}" />
```

This line will retrieve the optimized image URL.

## API

For now, there are only four main methods in Picasso:

### `optimize(string|array $image, string|array $dimension, string $disk = null)`

This method creates optimized images in desired dimensions for given images or image.

It accepts an array of image paths or a single image path.
It accepts an array of valid dimensions (as defined in the configuration) or a single dimension
The last parameter is the disk where to save the optimized image.

### `get(string $image, string $dimension, string $disk = null)`

This method retrieves the optimized image for given original image path and desired dimension.

The last parameter is the disk on which to perform this operation.

### `drop(string $image, string|array $dimension, string $disk = null)`

Thi method deletes optimized images from storage for given image path and dimension or dimensions.

The last parameter is the disk on which to perform this operation.

### `purge(string $image, string $disk = null)`

Thi method deletes all optimized images from storage for given image path.

The last parameter is the disk on which to perform this operation.

## Sponsors & Backers

I would like to extend my thanks to the following sponsors & backers for funding my open-source journey. If you are interested in becoming a sponsor or backer, please visit the [Backers page](https://mariobasic.com/backers).

## Contributing

Thank you for considering contributing to Picasso! The contribution guide can be found [Here](https://mariobasic.com/contributing).

## Code of Conduct

In order to ensure that the open-source community is welcoming to all, please review and abide by the [Code of Conduct](https://mariobasic.com/code-of-conduct).

## License

Picasso is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).