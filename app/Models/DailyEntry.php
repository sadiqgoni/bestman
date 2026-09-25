<?php

namespace App\Models;

use Database\Factories\DailyEntryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyEntry extends Model
{
    /** @use HasFactory<DailyEntryFactory> */
    use HasFactory;

    protected $fillable = ['entry_date', 'staff_id', 'status', 'fuel_grand_total_liters', 'fuel_grand_total_amount', 'cash_amount', 'pos_amount', 'bank_deposit_amount', 'total_expenses', 'net_cash_revenue', 'gross_profit', 'net_profit'];

    protected function casts(): array
    {
        return ['entry_date' => 'date', 'fuel_grand_total_liters' => 'decimal:2', 'fuel_grand_total_amount' => 'decimal:2', 'cash_amount' => 'decimal:2', 'pos_amount' => 'decimal:2', 'bank_deposit_amount' => 'decimal:2', 'total_expenses' => 'decimal:2', 'net_cash_revenue' => 'decimal:2', 'gross_profit' => 'decimal:2', 'net_profit' => 'decimal:2'];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function pumpReadings(): HasMany
    {
        return $this->hasMany(PumpReading::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
