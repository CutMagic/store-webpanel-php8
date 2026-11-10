<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * [multimod] Compte admin du panneau (table webpanel_users).
 * Remplace App\User + Entrust (plus disponible pour Laravel 12) : mêmes tables qu'avant,
 * une installation faite avec l'ancienne version reste utilisable.
 */
class User extends Authenticatable
{
    use Notifiable; // [php82] envoi du mail "mot de passe oublié"

    protected $connection = 'webpanel';
    protected $table = 'users';

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    /** Le compte a-t-il cette permission (via un de ses rôles) ? */
    public function hasPermission($name)
    {
        foreach ($this->roles()->with('perms')->get() as $role) {
            if ($role->perms->contains('name', $name)) {
                return true;
            }
        }
        return false;
    }

    public function getRoleListAttribute()
    {
        return $this->roles->pluck('id')->all();
    }
}
