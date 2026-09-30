<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google Analytics (gtag.js)
    |--------------------------------------------------------------------------
    |
    | Set the GA4 measurement id here to inject the Google tag on every page.
    | It reads GOOGLE_ANALYTICS_ID from the environment, so each environment
    | can track (or not track) independently. Leave it null to skip rendering
    | the snippet entirely.
    |
    | Example: G-0E22VXQQ94
    |
    */

    'measurement_id' => env('GOOGLE_ANALYTICS_ID'),

];
