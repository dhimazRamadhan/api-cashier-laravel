<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seats extends Model
{
    use HasFactory;
    protected $table = 'seats';
    protected $primarykey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'id', 'number'
    ];

    public function seats(): HasMany
    {
        return $this->hasMany(transactions::class, 'seat_id', 'id');
    }
}
