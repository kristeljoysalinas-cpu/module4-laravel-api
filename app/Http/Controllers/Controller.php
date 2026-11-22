<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'IT 255 Module 4 - Users REST API',
    description: 'Laravel 12 REST API for listing and creating users (Activity 4.1). POST requests require an API key in the X-API-KEY header.'
)]
#[OA\Server(url: 'http://127.0.0.1:8000', description: 'Local development server')]
#[OA\SecurityScheme(
    securityScheme: 'ApiKeyAuth',
    type: 'apiKey',
    in: 'header',
    name: 'X-API-KEY',
    description: 'API key required to create users'
)]
abstract class Controller
{
    //
}
