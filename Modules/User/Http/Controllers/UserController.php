<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\User\Models\User;
use Modules\Core\Traits\RespondsWithJson;
use Modules\User\Http\Requests\UserCreateRequest;
use Modules\User\Http\Resources\UserResource;
use Modules\User\Repositories\Contracts\UserInterface;
use Modules\User\Repositories\Eloquent\UserRepository;

class UserController extends Controller
{
    use RespondsWithJson;
    protected $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    /**
     * @OA\Get(
     *   path="/api/users",
     *   tags={"Users"},
     *   summary="Get list of users",
     *   description="Returns all users with optional search, pagination, and sorting.",
     *   @OA\Parameter(
     *     name="search",
     *     in="query",
     *     description="Search query for users.",
     *     @OA\Schema(type="string"),
     *     required=false
     *   ),
     *   @OA\Parameter(
     *     name="per_page",
     *     in="query",
     *     description="Number of users per page.",
     *     @OA\Schema(type="integer", default=15),
     *     required=false
     *   ),
     *   @OA\Parameter(
     *     name="dir",
     *     in="query",
     *     description="Direction of sorting (asc or desc).",
     *     @OA\Schema(type="string", enum={"asc", "desc"}, default="asc"),
     *     required=false
     *   ),
     *   @OA\Parameter(
     *     name="sort_by",
     *     in="query",
     *     description="Field to sort users by.",
     *     @OA\Schema(type="string", default="created_at"),
     *     required=false
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Successful operation",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="Users fetched successfully"),
     *     )
     *   ),
     *   security={{"bearerAuth":{}}}
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $searchQuery = $request->input('search', '');
        $perPage = $request->input('per_page', 15);
        $direction = $request->input('dir', 'asc');
        $sortBy = $request->input('sort_by', 'created_at');

        $users = $this->userRepository->all($searchQuery, $perPage, $direction, $sortBy);
        $usersResource = UserResource::collection($users);
        return $this->sendSuccess($usersResource, "Users fetched successfully", 200);
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Create a new user",
     *     @OA\Parameter(
     *         name="first_name",
     *         in="query",
     *         description="User's first name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *      @OA\Parameter(
     *         name="last_name",
     *         in="query",
     *         description="User's last name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password_confirmation",
     *         in="query",
     *         description="User's confirm password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *      @OA\Parameter(
     *         name="role_id",
     *         in="query",
     *         description="User's role",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="201", description="User registered successfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function create(UserCreateRequest $request): JsonResponse
    {
        try{
            $userCreated = $this->userRepository->create($request->validated());
            $user = new UserResource($userCreated);
            return $this->sendSuccess($user, "Users created successfully", 200);
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return $this->sendError('User registration failed', ['error' => 'An error occurred while creating the user: '. $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *   path="/api/users",
     *   tags={"Users"},
     *   summary="Get list of users",
     *   description="Returns all users with optional search, pagination, and sorting.",
    *    @OA\Parameter(
     *         name="username",
     *         in="query",
     *         description="User's username",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="Successful operation",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="Users fetched successfully"),
     *     )
     *   ),
     *   security={{"bearerAuth":{}}}
     * )
     */
    public function show(Request $request): JsonResponse
    {
        $username = $request->input('username', '');

        $user = $this->userRepository->find($username);

        if (!$user) {
            return $this->sendError('User not found', [], Response::HTTP_NOT_FOUND);
        }
    
        $userResource = new UserResource($user);
        return $this->sendSuccess($userResource, "User fetched successfully", 200);
    }
}
