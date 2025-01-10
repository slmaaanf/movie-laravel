<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory, HasUuids;

    // Kolom yang dapat diisi (mass assignable)
    protected $fillable = ['name'];

    // Relasi ke model Movie
    public function movies()
    {
        return $this->hasMany(Movie::class);
    }
}