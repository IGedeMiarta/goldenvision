<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function cart(){
        return $this->hasMany(UserChart::class);
    }
    public function order_detail(){
        return $this->hasMany(UserChart::class);
    }
}
