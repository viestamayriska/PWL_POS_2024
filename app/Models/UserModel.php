<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    use HasFactory;

    protected $table = 'm_user';
    protected $primaryKey = 'user_id';

    // Mendefinisikan nama tabel yang digunakan oleh model ini
    // Mendefinisikan primary key dari tabel yang digunakan
}
