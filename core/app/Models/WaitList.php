<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaitList extends Model
{
    use HasFactory;
    protected $table = 'wait_list';
    protected $guarded = ['id'];
}
