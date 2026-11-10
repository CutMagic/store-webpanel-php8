<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** [multimod] Rôle du panneau (table webpanel_roles), remplace EntrustRole. */
class Role extends Model
{
    protected $connection = 'webpanel';
    protected $table = 'roles';
    protected $fillable = ['name', 'display_name', 'description'];

    public function perms()
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
    }

    public function getPermissionListAttribute()
    {
        return $this->perms->pluck('id')->all();
    }
}
