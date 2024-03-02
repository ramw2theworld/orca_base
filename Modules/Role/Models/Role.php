<?php

namespace Modules\Role\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Modules\Permission\Models\Permission;
use Modules\User\Models\User;

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
        // Define your fillable attributes here
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
