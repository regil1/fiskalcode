<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Tentukan kolom yang boleh diisi (mass assignable)
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'budget_limit',
    ];

    // Relasi ke User (satu kategori milik satu user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Transactions (satu kategori punya banyak transaksi)
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
