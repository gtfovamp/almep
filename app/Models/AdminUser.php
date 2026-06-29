<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminUser extends Model
{
    protected $table = 'admin_users';
    public $timestamps = true; // has created_at & updated_at

    protected $fillable = ['username', 'password_hash'];

    protected $hidden = ['password_hash'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(AdminSession::class, 'user_id');
    }
}
