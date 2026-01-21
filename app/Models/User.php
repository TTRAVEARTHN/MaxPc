<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'role',
    ];

    // schovane polia pri serializacii
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // pretypovanie poli (vratane hashed password)
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // vztah na kosik
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    // vztah na objednavky
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // vztah na oblubene produkty
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
}
