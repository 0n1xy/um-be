<?php

return [
    'paths' => [ 'api/*'], // Cho phép CORS cho tất cả các API
    'allowed_methods' => ['*'], // Cho phép tất cả phương thức (GET, POST, PUT, DELETE...)
    'allowed_origins' => ['http://localhost:5173', 'https://um-fe.vercel.app/'], // Chỉ cho phép truy cập từ React frontend
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'], // Cho phép tất cả headers
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Hỗ trợ cookie/session trong request
];
