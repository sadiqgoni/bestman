<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Pump;
use App\Models\Tank;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedUsers();
        $products = $this->seedProducts();
        $tanks = $this->seedTanks($products);
        $this->seedPumps($products, $tanks);
    }

    private function seedUsers(): void
    {
        $accounts = [
            ['name' => 'Admin', 'email' => 'admin@gmail.com', 'role' => 'ADMIN'],
            ['name' => 'Staff', 'email' => 'staff@gmail.com', 'role' => 'STAFF'],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'role' => $account['role'],
                    'password' => Hash::make('12345678'),
                    'email_verified_at' => now(),
                ],
            );
        }
    }

    /**
     * @return array<string, Product>
     */
    private function seedProducts(): array
    {
        $products = [
            'PMS' => ['base' => 617, 'buying' => 580],
            'DIESEL' => ['base' => 1150, 'buying' => 1080],
            'KEROSENE' => ['base' => 1200, 'buying' => 1100],
        ];

        $records = [];

        foreach ($products as $type => $prices) {
            $records[$type] = Product::updateOrCreate(
                ['type' => $type],
                ['base_price_per_liter' => $prices['base'], 'buying_price_per_liter' => $prices['buying']],
            );
        }

        return $records;
    }

    /**
     * @param  array<string, Product>  $products
     * @return array<string, Tank>
     */
    private function seedTanks(array $products): array
    {
        $tanks = [
            'PMS Tank 1' => ['product' => 'PMS', 'capacity_liters' => 33000, 'current_stock_liters' => 20000],
            'PMS Tank 2' => ['product' => 'PMS', 'capacity_liters' => 33000, 'current_stock_liters' => 30000],
            'Diesel Tank 1' => ['product' => 'DIESEL', 'capacity_liters' => 20000, 'current_stock_liters' => 12000],
            'Kerosene Tank 1' => ['product' => 'KEROSENE', 'capacity_liters' => 10000, 'current_stock_liters' => 5000],
        ];

        $records = [];

        foreach ($tanks as $name => $data) {
            $records[$name] = Tank::updateOrCreate(
                ['name' => $name],
                [
                    'product_id' => $products[$data['product']]->id,
                    'capacity_liters' => $data['capacity_liters'],
                    'current_stock_liters' => $data['current_stock_liters'],
                    'active' => true,
                ],
            );
        }

        return $records;
    }

    /**
     * @param  array<string, Product>  $products
     * @param  array<string, Tank>  $tanks
     */
    private function seedPumps(array $products, array $tanks): void
    {
        $pumps = [
            'Pump 1 (PMS)' => ['product' => 'PMS', 'tank' => 'PMS Tank 1'],
            'Pump 2 (PMS)' => ['product' => 'PMS', 'tank' => 'PMS Tank 2'],
            'Pump 3 (Diesel)' => ['product' => 'DIESEL', 'tank' => 'Diesel Tank 1'],
            'Pump 4 (Kerosene)' => ['product' => 'KEROSENE', 'tank' => 'Kerosene Tank 1'],
        ];

        foreach ($pumps as $name => $data) {
            Pump::updateOrCreate(
                ['name' => $name],
                [
                    'product_id' => $products[$data['product']]->id,
                    'tank_id' => $tanks[$data['tank']]->id,
                    'active' => true,
                ],
            );
        }
    }
}
