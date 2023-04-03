<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailsTransactions extends Model
{
    use HasFactory;
    protected $table = 'details_transactions';
    protected $primarykey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'id', 'transactions_id', 'menu_id', 'price', 'qty', 'subtotal'
    ];
    
    public function transactions() : belongsTo 
    {
        return $this->belongsTo(detailsTransactions::class, 'transaction_id', 'id');
    }

    public function menus() : belongsTo
    {
        return $this->belongsTo(detailsTransactions::class, 'id_menu', 'id');
    }
}
