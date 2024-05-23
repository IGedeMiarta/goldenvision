<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiliError extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'fili_err_migrate';
}
