<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use Notifiable, HasRoles, HasApiTokens;

    protected $fillable = ['name', 'email', 'password', 'company_id', 'companies_users_id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'companies_users');
    }
    public function comapanies_users()
    {
        return $this->belongsToMany(CompanyUser::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

}

