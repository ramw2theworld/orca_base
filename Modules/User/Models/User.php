<?php

namespace Modules\User\Models;

use App\Http\Middleware\Authenticate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Modules\Role\Models\Role;
use Spatie\Permission\Traits\HasRoles;


/**
 * @OA\Schema(
 *     title="User",
 *     description="User model",
 *     @OA\Xml(name="User"),
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="first_name", type="string", example="John"),
 *     @OA\Property(property="last_name", type="string", example="Doe"),
 *     @OA\Property(property="email", type="string", example="john.doe@example.com"),
 *     @OA\Property(property="username", type="string", example="johndoe"),
 *     @OA\Property(property="status", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-04T13:42:53.000000Z"),
 *     @OA\Property(property="role_id", type="integer", example=2),
 *     @OA\Property(property="password", type="string", example="password01"),
 *     @OA\Property(property="password_confirmation", type="string", example="password01")
 * )
 */
class User extends Model
{
    use HasFactory, Notifiable, HasRoles;

    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'status',
        'role_id',
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

    protected $guarded = [];

    public function role() {
        return $this->belongsTo(Role::class, 'role_id');
    }

    protected static function newFactory()
    {
        return \Modules\User\Database\Factories\UserFactory::new();
    }

    public function guardName(){
        return "api";
    }
}