<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $table = "product";
    protected $fillable = [
        'code', 'name', 'image', 'description', 'price'
    ];
}
