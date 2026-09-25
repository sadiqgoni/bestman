<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = ['type', 'base_price_per_liter', 'buying_price_per_liter'];

    protected function casts(): array
    {
        return ['base_price_per_liter' => 'decimal:2', 'buying_price_per_liter' => 'decimal:2'];
    }

    public function tanks(): HasMany
    {
        return $this->hasMany(Tank::class);
    }

    public function pumps(): HasMany
    {
        return $this->hasMany(Pump::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(TankerDelivery::class);
    }
}
