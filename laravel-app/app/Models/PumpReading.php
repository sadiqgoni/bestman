<?php

namespace App\Models;

use Database\Factories\PumpReadingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PumpReading extends Model
{
    /** @use HasFactory<PumpReadingFactory> */
    use HasFactory;

    protected $fillable = ['daily_entry_id', 'pump_id', 'opening_reading', 'closing_reading', 'liters_sold', 'unit_selling_price', 'total_amount', 'base_price_at_entry', 'price_variance'];

    protected function casts(): array
    {
        return ['opening_reading' => 'decimal:2', 'closing_reading' => 'decimal:2', 'liters_sold' => 'decimal:2', 'unit_selling_price' => 'decimal:2', 'total_amount' => 'decimal:2', 'base_price_at_entry' => 'decimal:2', 'price_variance' => 'boolean'];
    }

    public function dailyEntry(): BelongsTo
    {
        return $this->belongsTo(DailyEntry::class);
    }

    public function pump(): BelongsTo
    {
        return $this->belongsTo(Pump::class);
    }
}
