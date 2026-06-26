<?php

return [
    'secret' => env('NOCAPTCHA_SECRET'),
    'sitekey' => env('NOCAPTCHA_SITEKEY'),
    'score_threshold' => (float) env('NOCAPTCHA_SCORE_THRESHOLD', 0.5),
    'options' => [
        'timeout' => 5,
    ],
];
