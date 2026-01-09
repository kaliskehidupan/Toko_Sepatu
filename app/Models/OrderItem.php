<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi lewat Controller
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
    ];

    /**
     * Relasi balik ke Order (Header pesanan)
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi ke Product (Untuk ambil nama, gambar, dll)
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
