<?php

namespace Modules\Role\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Role\Http\Requests\RoleCreateRequest;
use Modules\Role\Http\Resources\RoleResource;
use Modules\Role\Repositories\Contracts\RoleRepositoryInterface;
use Modules\Role\Repositories\Eloquent\RoleRepository;

/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 * )
 */
class RoleController extends Controller
{
    protected $roleRepository;
    public function __construct(RoleRepositoryInterface $roleRepository){
        $this->roleRepository = $roleRepository;
    }

    /**
     * @OA\Get(
     *   path="/api/roles",
     *   tags={"Roles"},
     *   summary="Get list of roles",
     *   description="Returns all roles with optional search, pagination, and sorting.",
     *   @OA\Parameter(
     *     name="search",
     *     in="query",
     *     description="Search query for roles.",
     *     @OA\Schema(type="string"),
     *     required=false
     *   ),
     *   @OA\Parameter(
     *     name="per_page",
     *     in="query",
     *     description="Number of roles per page.",
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
     *     description="Field to sort roles by.",
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
     *         @OA\Items(ref="#/components/schemas/Role")
     *       ),
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="roles fetched successfully"),
     *       @OA\Property(property="links", type="object",
     *         @OA\Property(property="first", type="string", example="http://api.example.com/api/roles?page=1"),
     *         @OA\Property(property="last", type="string", example="http://api.example.com/api/roles?page=10"),
     *         @OA\Property(property="prev", type="string", example="null"),
     *         @OA\Property(property="next", type="string", example="http://api.example.com/api/roles?page=2")
     *       ),
     *       @OA\Property(property="meta", type="object",
     *         @OA\Property(property="current_page", type="integer", example=1),
     *         @OA\Property(property="from", type="integer", example=1),
     *         @OA\Property(property="last_page", type="integer", example=10),
     *         @OA\Property(property="path", type="string", example="http://api.example.com/api/roles"),
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

        $roles = $this->roleRepository->all($searchQuery, $perPage, $direction, $sortBy);
        $resource = RoleResource::collection($roles);
        return $this->sendSuccess($resource, "Roles fetched successfully", 200);
    }

    /**
     * @OA\Post(
     *     path="/api/roles",
     *     tags={"Roles"},
     *     summary="Create a new role",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Role's first name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *    @OA\Response(
     *     response=200,
     *     description="Role Created Successfully",
     *     @OA\JsonContent(ref="#/components/schemas/Role")
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
    public function create(RoleCreateRequest $request): JsonResponse
    {
        try{
            $roleCreated = $this->roleRepository->create($request->validated());
            $role = new RoleResource($roleCreated);
            return $this->sendSuccess($role, "Role created successfully!", 200);
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return $this->sendError('Role registration failed', ['error' => 'An error occurred while creating the user: '. $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *   path="/api/roles/{slug}",
     *   tags={"Roles"},
     *   summary="Get a single role by slug",
     *   description="Returns a single role by slug.",
     *   @OA\Parameter(
     *     name="slug",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Successful operation",
     *     @OA\JsonContent(ref="#/components/schemas/Role")
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Role not found"
     *   ),
     *   security={{"bearerAuth":{}}}
     * )
     */
    public function show(string $slug): JsonResponse
    {
        if(!$slug){
            return $this->sendError('Slug is required', [], Response::HTTP_BAD_REQUEST);
        }

        $role = $this->roleRepository->find($slug);

        if (!$role) {
            return $this->sendError('Role not found', [], Response::HTTP_NOT_FOUND);
        }
    
        $roleResource = new RoleResource($role);
        return $this->sendSuccess($roleResource, "Role fetched successfully", 200);
    }


    /**
     * @OA\Put(
     *   path="/api/roles/{slug}",
     *   tags={"Roles"},
     *   summary="Update an existing role",
     *   description="Updates the role identified by the given slug.",
     *   @OA\Parameter(
     *     name="slug",
     *     in="path",
     *     required=true,
     *     description="slug of the role to update",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     description="Data for updating the role",
     *     @OA\JsonContent(ref="#/components/schemas/Role")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Role updated successfully",
     *     @OA\JsonContent(ref="#/components/schemas/Role")
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Role not found"
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Validate your inputs"
     *   ),
     *   security={{"bearerAuth":{}}}
     * )
     */
    public function update(RoleCreateRequest $request, string $slug): JsonResponse
    {
        try {
            $validated = $request->validated();

            $role = $this->roleRepository->update($slug, $validated);
            if (!$role) {
                return $this->sendError('Role not found', [], Response::HTTP_NOT_FOUND);
            }
            $roleResource = new RoleResource($role);
            return $this->sendSuccess($roleResource, "Role updated successfully", 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->sendError('Role update failed', ['error' => 'An error occurred while updating the role: '. $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/roles/{slug}",
     *   tags={"Roles"},
     *   summary="Delete a role",
     *   description="Deletes a role identified by slug.",
     *   @OA\Parameter(
     *     name="slug",
     *     in="path",
     *     description="slug of the role to be deleted",
     *     required=true,
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Role deleted successfully",
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
     *         example="Role deleted successfully"
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Role not found",
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
     *         example="Role not found"
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
     *         example="An error occurred while deleting the role"
     *       )
     *     )
     *   ),
     *   security={{"bearerAuth":{}}}
     * )
     */
    public function delete(string $slug): JsonResponse
    {
        try {
            $this->roleRepository->delete($slug);
            return $this->sendSuccess([], "Role deleted successfully", 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->sendError('Role deletion failed', ['error' => 'An error occurred while deleting the role: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
