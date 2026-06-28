<?php

/**
 * Optional importmap entry when importing consent CSS from app.js.
 * ConsentBanner auto-loads this file via asset() — consumers do not need importmap wiring.
 */
return [
    'privacy-settings-bundle/styles/privacy-settings-consent.css' => [
        'path' => './assets/styles/privacy-settings-consent.css',
        'type' => 'css',
    ],
    'privacy-settings-bundle/styles/privacy-settings-media.css' => [
        'path' => './assets/styles/privacy-settings-media.css',
        'type' => 'css',
    ],
];
