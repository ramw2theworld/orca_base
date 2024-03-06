<?php

namespace Modules\Permission\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Permission\Repositories\Contracts\PermissionRepositoryInterface;
use Modules\Permission\Http\Requests\PermissionCreateRequest;
use Modules\Permission\Http\Resources\PermissionResource;
use Modules\Role\Http\Requests\RoleCreateRequest;

class PermissionController extends Controller
{
    protected $permissionRepository;
    public function __construct(PermissionRepositoryInterface $permissionRepository){
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * @OA\Get(
     *   path="/api/permissions",
     *   tags={"Permissions"},
     *   summary="Get list of permissions",
     *   description="Returns all permissions with optional search, pagination, and sorting.",
     *   @OA\Parameter(
     *     name="search",
     *     in="query",
     *     description="Search query for permissions.",
     *     @OA\Schema(type="string"),
     *     required=false
     *   ),
     *   @OA\Parameter(
     *     name="per_page",
     *     in="query",
     *     description="Number of permissions per page.",
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
     *     description="Field to sort permissions by.",
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
     *         @OA\Items(ref="#/components/schemas/Permission")
     *       ),
     *       @OA\Property(property="success", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="permissions fetched successfully"),
     *       @OA\Property(property="links", type="object",
     *         @OA\Property(property="first", type="string", example="http://api.example.com/api/permissions?page=1"),
     *         @OA\Property(property="last", type="string", example="http://api.example.com/api/permissions?page=10"),
     *         @OA\Property(property="prev", type="string", example="null"),
     *         @OA\Property(property="next", type="string", example="http://api.example.com/api/permissions?page=2")
     *       ),
     *       @OA\Property(property="meta", type="object",
     *         @OA\Property(property="current_page", type="integer", example=1),
     *         @OA\Property(property="from", type="integer", example=1),
     *         @OA\Property(property="last_page", type="integer", example=10),
     *         @OA\Property(property="path", type="string", example="http://api.example.com/api/permissions"),
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

        $permissions = $this->permissionRepository->all($searchQuery, $perPage, $direction, $sortBy);
        $resource = PermissionResource::collection($permissions);
        return $this->sendSuccess($resource, "Permissions fetched successfully", 200);
    }

    /**
     * @OA\Post(
     *     path="/api/permissions",
     *     tags={"Permissions"},
     *     summary="Create a new permission",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Permission's first name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *    @OA\Response(
     *     response=200,
     *     description="Permission Created Successfully",
     *     @OA\JsonContent(ref="#/components/schemas/Permission")
     *   ),
    *   @OA\Response(
    *     response=404,
    *     description="Permission not found"
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
            $permissionCreated = $this->permissionRepository->create($request->validated());
            $permission = new PermissionResource($permissionCreated);
            return $this->sendSuccess($permission, "Permission created successfully!", 200);
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return $this->sendError('Permission registration failed', ['error' => 'An error occurred while creating the user: '. $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *   path="/api/permissions/{slug}",
     *   tags={"Permissions"},
     *   summary="Get a single permission by slug",
     *   description="Returns a single permission by slug.",
     *   @OA\Parameter(
     *     name="slug",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Successful operation",
     *     @OA\JsonContent(ref="#/components/schemas/Permission")
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Permission not found"
     *   ),
     *   security={{"bearerAuth":{}}}
     * )
     */
    public function show(string $slug): JsonResponse
    {
        if(!$slug){
            return $this->sendError('Slug is required', [], Response::HTTP_BAD_REQUEST);
        }

        $permission = $this->permissionRepository->find($slug);

        if (!$permission) {
            return $this->sendError('Permission not found', [], Response::HTTP_NOT_FOUND);
        }
    
        $permissionResource = new PermissionResource($permission);
        return $this->sendSuccess($permissionResource, "Permission fetched successfully", 200);
    }


    /**
     * @OA\Put(
     *   path="/api/permissions/{slug}",
     *   tags={"Permissions"},
     *   summary="Update an existing permission",
     *   description="Updates the permission identified by the given slug.",
     *   @OA\Parameter(
     *     name="slug",
     *     in="path",
     *     required=true,
     *     description="slug of the permission to update",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     description="Data for updating the permission",
     *     @OA\JsonContent(ref="#/components/schemas/Permission")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Permission updated successfully",
     *     @OA\JsonContent(ref="#/components/schemas/Permission")
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Permission not found"
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

            $permission = $this->permissionRepository->update($slug, $validated);
            if (!$permission) {
                return $this->sendError('Permission not found', [], Response::HTTP_NOT_FOUND);
            }
            $permissionResource = new PermissionResource($permission);
            return $this->sendSuccess($permissionResource, "Permission updated successfully", 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->sendError('Permission update failed', ['error' => 'An error occurred while updating the permission: '. $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/permissions/{slug}",
     *   tags={"Permissions"},
     *   summary="Delete a permission",
     *   description="Deletes a permission identified by slug.",
     *   @OA\Parameter(
     *     name="slug",
     *     in="path",
     *     description="slug of the permission to be deleted",
     *     required=true,
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Permission deleted successfully",
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
     *         example="Permission deleted successfully"
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Permission not found",
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
     *         example="Permission not found"
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
     *         example="An error occurred while deleting the permission"
     *       )
     *     )
     *   ),
     *   security={{"bearerAuth":{}}}
     * )
     */
    public function delete(string $slug): JsonResponse
    {
        try {
            $this->permissionRepository->delete($slug);
            return $this->sendSuccess([], "Permission deleted successfully", 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->sendError('Permission deletion failed', ['error' => 'An error occurred while deleting the permission: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
