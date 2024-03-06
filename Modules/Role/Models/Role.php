<?php

namespace Modules\Role\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Modules\Permission\Models\Permission;
use Modules\User\Models\User;

/**
 * @OA\Schema(
 *     title="Role",
 *     description="Role model",
 *     @OA\Xml(name="Role"),
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Admin"),
 *     @OA\Property(property="slug", type="string", example="admin"),
 *     @OA\Property(property="status", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-04T13:42:53.000000Z"),
 * )
 */
class Role extends Model
{
    use HasFactory, Notifiable;

    protected $table='roles';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'status',
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // Define your hidden attributes here
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Define your casts here
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Role\Database\Factories\RoleFactory::new();
    }

    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function permissions() {
        return $this->belongsToMany(Permission::class);
    }
}
