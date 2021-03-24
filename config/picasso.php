<?php

return [
    'dimensions' => [

        // Replace this with your own dimension requirements
        'example_dimension_name' => [
            'width' => 1920,
            'height' => 1080
        ],

        // Dot notation example.
        // Usage: frontend.categories.index
        'frontend' => [
            'categories' => [
                'index' => [
                    'width' => 1280,
                    'height' => 720,
                    'format' => 'png'
                ],
                'show' => [
                    'width' => 1920,
                    'height' => 1080,
                    'quality' => 90
                ],
            ]
        ]

        // ...
    ],

    // Use this variable to set the default image quality.
    // 0-100
    'quality' => 60,

    /**
     * The readable image formats depend on the choosen driver (GD or Imagick) and your local configuration.
     * By default Intervention Image currently supports the following major formats.
     *
     * Image Formats:
     * JPEG, PNG, GIF, TIF, BMP, ICO, PSD, WebP*
     *
     * For WebP support GD driver must be used with PHP 5 >= 5.5.0 or PHP 7 in order to use imagewebp().
     * If Imagick is used, it must be compiled with libwebp for WebP support.
     */
    'format' => 'webp',

    /**
     * This the the place where you should keep your original full size images.
     *
     * In your Laravel application in `config/filesystem.php`, create a new disk under `disks`:
     *
     *      'originals' => [
     *          'driver' => 'local',
     *          'root' => storage_path('app/originals'),
     *      ],
     *
     * Or replace with the disk name where your original are located.
     *
     */
    'disk' => 'originals'
];
