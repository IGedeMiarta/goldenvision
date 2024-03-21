<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryAgent extends Model
{
    use HasFactory;
    protected $table = 'delivery_agents';
    protected $guarded = ['id'];

    public function order(){
        return $this->hasMany(ProductOrder::class);
    }
}
