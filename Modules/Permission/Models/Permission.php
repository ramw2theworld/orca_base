<?php

namespace Modules\Permission\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Modules\Role\Models\Role;
use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * @OA\Schema(
 *     title="Permission",
 *     description="Permission model",
 *     @OA\Xml(name="Permission"),
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Create User"),
 *     @OA\Property(property="slug", type="string", example="creat-user"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-04T13:42:53.000000Z"),
 * )
 */
class Permission extends SpatiePermission
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'status',
        'guard_name',

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
        return \Modules\Permission\Database\factories\PermissionFactory::new();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions', 'permission_id', 'role_id');
    }
}
