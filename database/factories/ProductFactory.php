<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $units = ['piece', 'kg', 'liter', 'meter', 'set', 'pack'];
        
        $purchasePrice = $this->faker->randomFloat(2, 5, 1000);
        $sellingPrice = $purchasePrice * $this->faker->randomFloat(2, 1.1, 2);
        
        $categoryId = Category::inRandomOrder()->first()->id ?? 1;
        $supplierId = Supplier::inRandomOrder()->first()->id ?? 1;
        
        return [
            'name' => $this->faker->words(3, true),
            'sku' => strtoupper(Str::random(3)) . '-' . $this->faker->numberBetween(100, 999),
            'description' => $this->faker->sentence(),
            'category_id' => $categoryId,
            'supplier_id' => $supplierId,
            'unit' => $this->faker->randomElement($units),
            'purchase_price' => $purchasePrice,
            'selling_price' => $sellingPrice,
            'current_stock' => $this->faker->numberBetween(0, 100),
            'alert_stock' => $this->faker->numberBetween(5, 20),
            'is_active' => true,
        ];
    }
}