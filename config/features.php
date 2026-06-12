<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Session Media Uploads
    |--------------------------------------------------------------------------
    |
    | Audio recordings are useful, but shared hosting storage and upload limits
    | make them a poor fit for the low-cost deployment path. Keep this disabled
    | until production storage is intentionally planned.
    |
    */
    'session_media_uploads' => env('FEATURE_SESSION_MEDIA_UPLOADS', false),
];
