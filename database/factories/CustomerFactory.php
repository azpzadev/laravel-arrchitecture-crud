<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Customer\Enums\CustomerStatus;
use App\Infrastructure\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'company' => fake()->optional(0.7)->company(),
            'status' => fake()->randomElement(CustomerStatus::cases()),
            'metadata' => [
                'source' => fake()->randomElement(['web', 'mobile', 'api', 'import']),
                'notes' => fake()->optional(0.3)->sentence(),
            ],
        ];
    }

    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => CustomerStatus::Active,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => CustomerStatus::Inactive,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => CustomerStatus::Suspended,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => CustomerStatus::Pending,
        ]);
    }
}
