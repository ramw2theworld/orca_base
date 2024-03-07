<?php

namespace Modules\Permission\Repositories\Eloquent;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Modules\Permission\Models\Permission;
use Modules\Permission\Repositories\Contracts\PermissionRepositoryInterface;
use Illuminate\Support\Str;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class PermissionRepository implements PermissionRepositoryInterface
{
    protected $model;
    public function __construct(Permission $model){
        $this->model = $model;
    }

    public function all(string $search = null, int $per_page, string $dir, string $sortCol): LengthAwarePaginator
    {
        $query = $this->model
        ->select(
            'id', 
            'name', 
            'slug',  
            'created_at',
        );
    
        if (!empty($search)) {
            $lowerSearch = strtolower($search);

            $query->where(function ($query) use ($lowerSearch) {
                $query->where(DB::raw('LOWER(name)'), 'like', "%{$lowerSearch}%")
                      ->orWhere(DB::raw('LOWER(slug)'), 'like', "%{$lowerSearch}%");
            });
        }
        
        return $query->orderBy($sortCol, $dir)->paginate($per_page);
    }

    public function find(string $slug): ?Permission
    {
        try{
            return $this->model->where("slug", $slug)->first();
        }
        catch(ModelNotFoundException $e){
            throw new ModelNotFoundException("User does not exist!");
        }
        catch(Exception $e){
            throw new $e($e->getMessage());
        }
    }

    public function create(array $data): Permission
    {
        DB::beginTransaction();
        try{
            $slug = Str::slug($data['name'], '-');

            $role = $this->model::create([
                'name' => $data['name'],
                'slug' => $slug,
                'guard_name' => 'web'

            ]);

            DB::commit();
            return $role;
        }catch(Exception $ex){
            DB::rollBack();
            $errorCode = is_numeric($ex->getCode()) ? (int)$ex->getCode() : 0;
            throw new \Exception($ex->getMessage(), $errorCode, $ex);
        }
    }

    public function update(string $slug, array $data): ?Permission
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
            $permission = $this->model::where('slug', $slug)->first();
            if (!$permission) {   
                throw new ModelNotFoundException('Permissions not found');
            }
            $this->model->delete();
        } catch (Exception $ex) {
            throw new $ex($ex->getMessage());
        }
    }
}
