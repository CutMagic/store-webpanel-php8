<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** [multimod] Permission du panneau (table webpanel_permissions), remplace EntrustPermission. */
class Permission extends Model
{
    protected $connection = 'webpanel';
    protected $table = 'permissions';
    protected $fillable = ['name', 'display_name', 'description'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role', 'permission_id', 'role_id');
    }
}
