<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // 1. Daftarkan kolom yang boleh diisi (WAJIB)
    protected $fillable = [
        'user_id',
        'order_number',
        'total_price',
        'status',
        'address',
    ];

    // 2. Relasi ke User (Siapa yang beli)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 3. Relasi ke OrderItems (Apa saja yang dibeli)
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
