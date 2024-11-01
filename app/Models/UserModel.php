<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; 
use Illuminate\Foundation\Auth\User as Authenticatable;


class UserModel extends Authenticatable 
{

    protected $table = 'm_user';
    protected $primaryKey = 'user_id';
    protected $fillable = ['username', 'password', 'nama', 'level_id', 'profile_image', 'created_at', 'updated_at'];

    protected $hidden = ['password']; // Tidak ditampilkan saat select
    protected $casts = ['password' => 'hashed']; // Password akan di-hash secara otomatis

    // Relasi ke tabel level
public function level(): BelongsTo
{
    return $this->belongsTo(LevelModel::class, 'level_id', 'level_id');
}

// Mendapatkan nama role
public function getRoleName(): string
{
    return $this->level->level_nama;
}

// Cek apakah user memiliki role tertentu
public function hasRole($role): bool
{
    return $this->level->level_kode == $role;
}

   
}
