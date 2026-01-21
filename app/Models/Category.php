<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $fillable = [
        'name',
        'description',
    ];

    // vztah kategoria ma viac produktov
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
