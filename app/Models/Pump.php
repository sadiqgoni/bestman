<?php

namespace App\Models;

use Database\Factories\PumpFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pump extends Model
{
    /** @use HasFactory<PumpFactory> */
    use HasFactory;

    protected $fillable = ['name', 'product_id', 'tank_id', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function tank(): BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }

    public function readings(): HasMany
    {
        return $this->hasMany(PumpReading::class);
    }
}
