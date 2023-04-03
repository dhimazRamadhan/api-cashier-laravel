<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;
    protected $table = 'transactions';
    protected $primarykey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'id', 'transaction_date', 'user_id', 'seat_id', 'customer_name', 'status'
    ];

    public function user() : belongsTo
    {
        return $this->belongsTo(Transaction::class, 'user_id', 'id');
    }

    public function transaction() : hasMany
    {
        return $this->hasMany(detailsTransactions::class, 'transaction_id', 'id');
    }
}
