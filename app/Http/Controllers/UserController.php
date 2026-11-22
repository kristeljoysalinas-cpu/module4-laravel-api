<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Get(
        path: '/api/users',
        summary: 'List all users',
        tags: ['Users'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of users (passwords are never returned)',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/User'))
            ),
            new OA\Response(response: 429, description: 'Too many requests (rate limit: 60 per minute)'),
        ]
    )]
    public function index(): JsonResponse
    {
        return response()->json(User::all());
    }

    #[OA\Post(
        path: '/api/users',
        summary: 'Create a new user',
        security: [['ApiKeyAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 100, example: 'Juan Dela Cruz'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 100, example: 'juan@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8, example: 'secret123'),
                ]
            )
        ),
        tags: ['Users'],
        responses: [
            new OA\Response(response: 201, description: 'User created', content: new OA\JsonContent(ref: '#/components/schemas/User')),
            new OA\Response(response: 401, description: 'Missing or invalid API key'),
            new OA\Response(response: 422, description: 'Validation failed'),
            new OA\Response(response: 429, description: 'Too many requests (rate limit: 60 per minute)'),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        // Validation: name and email are required, email must be valid and unique,
        // password must be at least 8 characters.
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:8|max:100',
        ]);

        // Always answer in JSON (no redirect), even without an Accept header
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Only validated fields are saved; the password is hashed by the User model
        $user = User::create($validator->validated());

        return response()->json($user, 201);
    }
}
