<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingsGoal extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'user_id',
        'name',
        'target_amount',
        'current_amount',
    ];

    // Relasi ke User (Opsional, tapi bagus untuk OOP)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
