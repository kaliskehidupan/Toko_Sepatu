<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Mass assignment protection - semua kolom database harus didaftarkan di sini
    protected $fillable = [
        'category_id', // Penting agar bisa pilih kategori di admin
        'name',
        'description',
        'price',
        'stock',
        'image',
        'brand', // Penting agar brand sepatu bisa disimpan
    ];

    /**
     * Relasi ke Category
     * Menjelaskan bahwa satu produk dimiliki oleh satu kategori
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
