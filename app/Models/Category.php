<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Tambahkan 'slug' di sini supaya diizinkan masuk ke database
    protected $fillable = [
        'name',
        'slug'
    ];

    /**
     * Relasi ke Produk
     * Satu kategori memiliki banyak produk
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
