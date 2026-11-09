<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected UserServiceInterface $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function index(): JsonResponse
    {
        Log::info('All user profiles retrieved');

        $users = $this->userService->getAllUserProfiles();

        return response()->json([
            'success' => true,
            'message' => 'All user profiles retrieved successfully',
            'data' => UserResource::collection($users),
        ]);
    }
}
