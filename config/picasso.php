<?php

return [
    'dimensions' => [

        // Replace this with your own dimension requirements
        'example_dimension_name' => [
            'width' => 1920,
            'heigh' => 1080
        ],

        // Dot notation example.
        // Usage: frontend.categories.index
        'frontend' => [
            'categories' => [
                'index' => [
                    'width' => 1280,
                    'heigh' => 720
                ],
                'show' => [
                    'width' => 1920,
                    'heigh' => 1080
                ],
            ]
        ]

        // ...
    ],

    // Use this variable to set the default image quality.
    'quality' => 60
];