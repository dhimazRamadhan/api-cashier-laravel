<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menus extends Model
{
    use HasFactory;
    protected $table = 'menus';
    protected $primarykey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'id', 'name', 'type', 'description', 'image', 'price'
    ];

    public function menu(): HasMany
    {
        return $this->hasMany(detailsTransactions::class, 'menu_id', 'id');
    }
}
