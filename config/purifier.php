<?php

return [
    'encoding' => 'UTF-8',
    'finalize' => true,
    'cachePath' => storage_path('app/purifier'),
    'cacheFileMode' => 0755,
    'settings' => [
        'default' => [
            'URI.AllowedSchemes' => ['http' => true, 'https' => true, 'mailto' => true],
            'AutoFormat.RemoveEmpty' => true,
            'AutoFormat.Linkify' => false,
        ],
        'strip' => [
            'HTML.Allowed' => '',
            'HTML.AllowedAttributes' => '',
        ],
    ],
];
