<?php

namespace App\Models;

use Database\Factories\TankFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tank extends Model
{
    /** @use HasFactory<TankFactory> */
    use HasFactory;

    protected $fillable = ['name', 'product_id', 'capacity_liters', 'current_stock_liters', 'active'];

    protected function casts(): array
    {
        return ['capacity_liters' => 'decimal:2', 'current_stock_liters' => 'decimal:2', 'active' => 'boolean'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
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
