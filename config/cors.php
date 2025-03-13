<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'], 
    'allowed_origins' => ['https://um-fe.vercel.app'], 
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'], 
    'exposed_headers' => ['Authorization', 'X-CSRF-TOKEN'],
    'max_age' => 0,
    'supports_credentials' => true,  
];
