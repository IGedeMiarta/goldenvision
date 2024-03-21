<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOrder extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function detail(){
        return $this->hasMany(ProductOrderDetail::class,'order_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function agent(){
        return $this->belongsTo(DeliveryAgent::class,'agen');
    }
}
