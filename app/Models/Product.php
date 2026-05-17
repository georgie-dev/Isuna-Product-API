<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable =[
        "name",
        "description",
        "price",
        "stock",
    ];

    protected $casts =[
        "price"=> "decimal:2",
        "stock"=> 'integer',
        "deleted_at"=> 'datetime',
    ];
}
