<?php 

use Kirby\Cms\App as Kirby;

load([
    'rasteiner\opcache\OpcacheDriver' => 'OpcacheDriver.php',
], __DIR__ . '/lib');

Kirby::plugin('rasteiner/kirby-opcache', [
    'cacheTypes' => [
        'opcache' => \rasteiner\opcache\OpcacheDriver::class,
    ]
]);