<?php

namespace App\Models;

use Database\Factories\TankerDeliveryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TankerDelivery extends Model
{
    /** @use HasFactory<TankerDeliveryFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'PENDING';

    public const STATUS_CONFIRMED = 'CONFIRMED';

    public const STATUS_REJECTED = 'REJECTED';

    protected $fillable = ['tanker_plate_number', 'supplier_name', 'invoice_number', 'product_id', 'tank_id', 'waybill_liters', 'received_liters', 'variance_liters', 'buying_price_per_liter', 'total_cost', 'driver_notes', 'status', 'created_by_id', 'confirmed_by_id', 'confirmed_at'];

    protected function casts(): array
    {
        return ['waybill_liters' => 'decimal:2', 'received_liters' => 'decimal:2', 'variance_liters' => 'decimal:2', 'buying_price_per_liter' => 'decimal:2', 'total_cost' => 'decimal:2', 'confirmed_at' => 'datetime'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function tank(): BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by_id');
    }

    /**
     * Confirm a pending delivery: locks in the buying price, credits the
     * received litres to the tank, and updates the product's rolling cost.
     * Only ever moves stock for a delivery still in PENDING status.
     */
    public function confirm(User $admin, float $buyingPricePerLiter): void
    {
        if ($this->status !== self::STATUS_PENDING) {
            throw new RuntimeException('Only pending deliveries can be confirmed.');
        }

        DB::transaction(function () use ($admin, $buyingPricePerLiter): void {
            $tank = $this->tank()->lockForUpdate()->first();
            $tank->increment('current_stock_liters', (float) $this->received_liters);

            $this->product()->update(['buying_price_per_liter' => $buyingPricePerLiter]);

            $this->update([
                'status' => self::STATUS_CONFIRMED,
                'buying_price_per_liter' => $buyingPricePerLiter,
                'total_cost' => round($buyingPricePerLiter * (float) $this->received_liters, 2),
                'confirmed_by_id' => $admin->id,
                'confirmed_at' => now(),
            ]);
        });
    }

    public function reject(User $admin): void
    {
        if ($this->status !== self::STATUS_PENDING) {
            throw new RuntimeException('Only pending deliveries can be rejected.');
        }

        $this->update([
            'status' => self::STATUS_REJECTED,
            'confirmed_by_id' => $admin->id,
            'confirmed_at' => now(),
        ]);
    }
}
