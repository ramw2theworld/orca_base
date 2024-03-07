<?php

namespace Modules\Role\Repositories\Eloquent;

use Exception;
use Modules\Role\Models\Role;
use Modules\Role\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleRepository implements RoleRepositoryInterface
{
    protected $model;
    public function __construct(Role $model){
        $this->model = $model;
    }

    public function all(string $search = null, int $per_page, string $dir, string $sortCol): LengthAwarePaginator
    {
        $query = $this->model
        ->select(
            'id', 
            'name', 
            'slug', 
            'created_at'
        );
    
        // Notice the inclusion of 'role_id' above
        
        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->where("name", "like", "%{$search}%")
                    ->orWhere("slug", "like", "%{$search}%");
            });
        }
        
        return $query->orderBy($sortCol, $dir)->paginate($per_page);
    }

    public function find(string $slug): ?Role
    {
        try{
            return $this->model->where("slug", $slug)->first();
        }
        catch(ModelNotFoundException $e){
            throw new ModelNotFoundException("Role does not exist!");
        }
        catch(Exception $e){
            throw new $e($e->getMessage());
        }
    }

    public function create(array $data): Role
    {
        DB::beginTransaction();
        try{
            $slug = Str::slug($data['name'], '-');

            $role = $this->model::create([
                'name' => $data['name'],
                'slug' => $slug,
                'guard_name' => 'web',

            ]);
            DB::commit();
            return $role;
        }catch(Exception $ex){
            DB::rollBack();
            $errorCode = is_numeric($ex->getCode()) ? (int)$ex->getCode() : 0;
            throw new \Exception($ex->getMessage(), $errorCode, $ex);
        }
    }

    public function update(string $slug, array $data): ?Role
    {
        try {
            $role = $this->model::where('slug', $slug)->first();
            if (!$role) {
                throw new ModelNotFoundException('role not found');
            }

            $role->slug = Str::slug($data['name'], '-');    

            foreach ($data as $key => $value) {
                if (isset($data[$key])) {
                    $role->$key = $value;
                }
            }
            $role->save();
            
            return $role;
        } catch (\Exception $ex) {
            throw new $ex($ex->getMessage());
        }
    }

    public function delete(string $slug): void
    {
        try {
            $role = $this->model::where('slug', $slug)->first();
            if (!$role) {   
                throw new ModelNotFoundException('Role not found');
            }
            $this->model->delete();
        } catch (Exception $ex) {
            throw new $ex($ex->getMessage());
        }
    }
}
