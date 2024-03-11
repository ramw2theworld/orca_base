<?php

namespace Modules\User\Repositories\Eloquent;

use Exception;
use Modules\User\Models\User;
use Modules\User\Repositories\Contracts\UserInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Permission\Models\Permission;
use Modules\Role\Models\Role;

class UserRepository implements UserInterface
{
    protected $model;
    public function __construct(User $model){
        $this->model = $model;
    }

    public function all(string $search = null, int $per_page, string $dir, string $sortCol): LengthAwarePaginator
    {
        $query = $this->model::with('roles')
        ->select(
            'users.id', 
            'users.first_name', 
            'users.last_name', 
            'users.email', 
            'users.username', 
            'users.status', 
            'users.created_at'
        );
    
        // Notice the inclusion of 'role_id' above
        
        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->where("first_name", "like", "%{$search}%")
                    ->orWhere("last_name", "like", "%{$search}%")
                    ->orWhere("email", "like", "%{$search}%")
                    ->orWhere("username", "like", "%{$search}%");
            })
            ->orWhereHas('roles', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        
        return $query->orderBy($sortCol, $dir)->paginate($per_page);
    }

    public function find(string $username): ?User
    {
        try{

            return $this->model::whereRaw('lower(username) = ?', [strtolower($username)])
            ->with('roles.permissions', 'permissions')
            ->firstOrFail();

        }catch(ModelNotFoundException $e){
            throw new ModelNotFoundException("User does not exist!");
        }catch(Exception $e){
            throw new ModelNotFoundException("Server error!");
        }
    }

    public function create(array $data): User
    {
        DB::beginTransaction();
        try{
           $user = $this->model::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'username' => strtolower($data['first_name']) . strtolower($data['last_name']) . rand(1000, 9999),
                'status' => true,

            ]);
            $user->assignRole('User');

            // $user->load('roles');
            DB::commit();
            return $user->fresh();
        }catch(Exception $ex){
            DB::rollBack();
            $errorCode = is_numeric($ex->getCode()) ? (int)$ex->getCode() : 0;
            throw new \Exception($ex->getMessage(), $errorCode, $ex);
        }
    }

    public function update($username, array $data): ?User
    {
        try {
            $user = $this->model::where('username', $username)->first();
            if (!$user) {
                throw new ModelNotFoundException('User not found');
            }

            foreach ($data as $key => $value) {
                if (isset($data[$key])) {
                    $user->$key = $value;
                }
            }
            $user->save();
            
            return $user;
        } catch (\Exception $ex) {
            throw new $ex($ex->getMessage());
        }
    }

    public function delete(string $username): void
    {
        try {
            $user = $this->model::where('username', $username)->first();
            if (!$user) {   
                throw new ModelNotFoundException('User not found');
            }
            $this->model->delete();
        } catch (Exception $ex) {
            throw new $ex($ex->getMessage());
        }
    }

    public function attachDetachPermissionsToUser(string $username, array $data): ?User
    {
        try {
            $user = $this->model::where('username', $username)->first();
            if (!$user) {
                throw new ModelNotFoundException('User not found');
            }
            
            $permissionsSlugs = $data['permissions'];
            $action = $data['action'];
            
            $permissions = Permission::whereIn('slug', $permissionsSlugs)->get();
    
            if (count($permissions) === 0) {
                throw new \Exception("One or more permissions not found.");
            }
    
            if ($action === "detach") {
                $user->revokePermissionTo($permissions);
            } else {
                $user->givePermissionTo($permissions);
            }
            $user->load('permissions');
    
            return $user;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            throw new $ex($ex->getMessage());
        }
    }

    
    public function attachDetachRolesToUser(string $username, array $data): ?User
    {
        try {
            $user = $this->model::where('username', $username)->first();
            if (!$user) {
                throw new ModelNotFoundException('User not found');
            }
            
            $slugs = $data['roles'];
            $action = $data['action'];
            
            $roles = Role::whereIn('slug', $slugs)->get();
            if (count($roles) === 0) {
                throw new \Exception("One or more roles not found.");
            }
    
            foreach($roles as $role){
                if ($action === "attach") {
                    $user->assignRole($role);
                } else {
                    $user->removeRole($role);
                }
            }
            $user->load('roles');
            return $user;

        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            throw new $ex($ex->getMessage());
        }
    }
}
