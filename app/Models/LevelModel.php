<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelModel extends Model
{
    use HasFactory;

    protected $table = 'm_level'; // Mendefinisikan nama tabel yang digunakan oleh model ini
    protected $primaryKey = 'level_id'; // Mendefinisikan primarykey dari tabel yang digunakan
    protected $fillable = ['level_kode', 'level_nama'];
}