<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\User\Http\Requests\UserCreateRequest;
use Modules\User\Http\Resources\UserResource;
use Modules\User\Repositories\Contracts\UserInterface;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
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
     *       @OA\Property(
     *         property="data",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/User")
     *       ),
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="Users fetched successfully"),
     *       @OA\Property(property="links", type="object",
     *         @OA\Property(property="first", type="string", example="http://api.example.com/api/users?page=1"),
     *         @OA\Property(property="last", type="string", example="http://api.example.com/api/users?page=10"),
     *         @OA\Property(property="prev", type="string", example="null"),
     *         @OA\Property(property="next", type="string", example="http://api.example.com/api/users?page=2")
     *       ),
     *       @OA\Property(property="meta", type="object",
     *         @OA\Property(property="current_page", type="integer", example=1),
     *         @OA\Property(property="from", type="integer", example=1),
     *         @OA\Property(property="last_page", type="integer", example=10),
     *         @OA\Property(property="path", type="string", example="http://api.example.com/api/users"),
     *         @OA\Property(property="per_page", type="integer", example=15),
     *         @OA\Property(property="to", type="integer", example=15),
     *         @OA\Property(property="total", type="integer", example=150)
     *       )
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
        $resource = UserResource::collection($users);
        return $this->sendSuccess($resource, "Users fetched successfully", 200);
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
     *    @OA\Response(
     *     response=200,
     *     description="User Created Successfully",
     *     @OA\JsonContent(ref="#/components/schemas/User")
     *   ),
    *   @OA\Response(
    *     response=404,
    *     description="Role not found"
    *   ),
    *   @OA\Response(
    *     response=422,
    *     description="Please send valid data"
    *   ),
    *   security={{"bearerAuth":{}}}
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
     *   path="/api/users/{username}",
     *   tags={"Users"},
     *   summary="Get a single user by username",
     *   description="Returns a single user by username.",
     *   @OA\Parameter(
     *     name="username",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Successful operation",
     *     @OA\JsonContent(ref="#/components/schemas/User")
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="User not found"
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


    /**
     * @OA\Put(
     *   path="/api/users/{id}",
     *   tags={"Users"},
     *   summary="Update an existing user",
     *   description="Updates the user identified by the given ID.",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID of the user to update",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     description="Data for updating the user",
     *     @OA\JsonContent(ref="#/components/schemas/User")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="User updated successfully",
     *     @OA\JsonContent(ref="#/components/schemas/User")
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="User not found"
     *   ),
     *   security={{"bearerAuth":{}}}
     * )
     */
    public function update(UserCreateRequest $request, $id): JsonResponse
    {
        try {
            $validated = $request->validated();

            // Optionally hash the password if it's provided in the update request
            if (isset($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            }

            $user = $this->userRepository->update($id, $validated);

            if (!$user) {
                return $this->sendError('User not found', [], Response::HTTP_NOT_FOUND);
            }
            $userResource = new UserResource($user);
            return $this->sendSuccess($userResource, "User updated successfully", 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->sendError('User update failed', ['error' => 'An error occurred while updating the user: '. $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/users/{username}",
     *   tags={"Users"},
     *   summary="Delete a user",
     *   description="Deletes a user identified by username.",
     *   @OA\Parameter(
     *     name="username",
     *     in="path",
     *     description="Username of the user to be deleted",
     *     required=true,
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="User deleted successfully",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="success",
     *         type="boolean",
     *         example=true
     *       ),
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="User deleted successfully"
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="User not found",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="success",
     *         type="boolean",
     *         example=false
     *       ),
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="User not found"
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Internal Server Error",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="success",
     *         type="boolean",
     *         example=false
     *       ),
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="An error occurred while deleting the user"
     *       )
     *     )
     *   ),
     *   security={{"bearerAuth":{}}}
     * )
     */
    public function delete(string $username): JsonResponse
    {
        try {
            $this->userRepository->delete($username);
            return $this->sendSuccess([], "User deleted successfully", 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->sendError('User deletion failed', ['error' => 'An error occurred while deleting the user: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
