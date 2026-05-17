<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
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
        $adjectives = ['Premium', 'Luxury', 'Portable', 'Wireless', 'Ergonomic', 'Vintage', 'Modern', 'Classic', 'Smart', 'Pro'];
        $nouns = ['Chair', 'Desk', 'Lamp', 'Monitor', 'Keyboard', 'Headphones', 'Watch', 'Bag', 'Shoes', 'Table'];

        $name = fake()->randomElement($adjectives) . ' ' . fake()->randomElement($nouns);
        $features = [
            "Designed for maximum comfort and durability, this $name is perfect for everyday use.",
            "The $name features a sleek modern design with premium materials that last.",
            "Experience superior quality with the $name, built for professionals and enthusiasts alike.",
            "This $name combines style and functionality, making it an essential addition to your space.",
            "Crafted with precision, the $name delivers exceptional performance and reliability.",
            "The $name is engineered to meet the highest standards of quality and comfort.",
            "Whether at home or in the office, the $name adapts seamlessly to your lifestyle.",
            "Built to last, the $name offers unmatched value with its premium construction.",
        ];

        return [
            'name' => $name,
            'description' => fake()->randomElement($features),
            'price' => fake()->randomFloat(2, 10, 999),
            'stock' => fake()->numberBetween(0, 500),
        ];
    }
}
