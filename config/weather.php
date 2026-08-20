<?php

return [

    'api_key' => env('OPENWEATHER_API_KEY'),

    'base_url' => 'https://api.openweathermap.org',

    // "metric" gives temperatures in Celsius and wind speed in m/s.
    'units' => 'metric',

    // Seconds a weather/forecast/air quality response is cached for.
    'cache_ttl' => 600,

    'retry_times' => 3,

    'retry_sleep_ms' => 200,

];
