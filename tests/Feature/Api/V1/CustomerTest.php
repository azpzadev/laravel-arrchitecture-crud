<?php

declare(strict_types=1);

use App\Domain\Customer\Enums\CustomerStatus;
use App\Infrastructure\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Customer API', function () {

    describe('GET /api/v1/customers', function () {
        it('returns a paginated list of customers', function () {
            Customer::factory()->count(15)->create();

            $response = $this->getJson('/api/v1/customers');

            $response->assertOk()
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        '*' => ['uuid', 'name', 'email', 'status'],
                    ],
                    'meta' => ['current_page', 'last_page', 'per_page', 'total'],
                    'links',
                ]);
        });

        it('filters customers by status', function () {
            Customer::factory()->active()->count(3)->create();
            Customer::factory()->inactive()->count(2)->create();

            $response = $this->getJson('/api/v1/customers?status=active');

            $response->assertOk();
            expect($response->json('meta.total'))->toBe(3);
        });

        it('searches customers by name or email', function () {
            Customer::factory()->create(['name' => 'John Doe']);
            Customer::factory()->create(['name' => 'Jane Smith']);

            $response = $this->getJson('/api/v1/customers?search=John');

            $response->assertOk();
            expect($response->json('meta.total'))->toBe(1);
        });
    });

    describe('POST /api/v1/customers', function () {
        it('creates a new customer', function () {
            $data = [
                'name' => 'Test Customer',
                'email' => 'test@example.com',
                'phone' => '1234567890',
                'company' => 'Test Company',
            ];

            $response = $this->postJson('/api/v1/customers', $data);

            $response->assertCreated()
                ->assertJsonPath('success', true)
                ->assertJsonPath('data.name', 'Test Customer')
                ->assertJsonPath('data.email', 'test@example.com');

            $this->assertDatabaseHas('customers', [
                'email' => 'test@example.com',
            ]);
        });

        it('validates required fields', function () {
            $response = $this->postJson('/api/v1/customers', []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email']);
        });

        it('prevents duplicate email addresses', function () {
            Customer::factory()->create(['email' => 'existing@example.com']);

            $response = $this->postJson('/api/v1/customers', [
                'name' => 'New Customer',
                'email' => 'existing@example.com',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
        });
    });

    describe('GET /api/v1/customers/{customer}', function () {
        it('returns a single customer', function () {
            $customer = Customer::factory()->create();

            $response = $this->getJson("/api/v1/customers/{$customer->uuid}");

            $response->assertOk()
                ->assertJsonPath('success', true)
                ->assertJsonPath('data.uuid', $customer->uuid);
        });

        it('returns 404 for non-existent customer', function () {
            $response = $this->getJson('/api/v1/customers/non-existent-uuid');

            $response->assertNotFound();
        });
    });

    describe('PUT /api/v1/customers/{customer}', function () {
        it('updates an existing customer', function () {
            $customer = Customer::factory()->create();

            $response = $this->putJson("/api/v1/customers/{$customer->uuid}", [
                'name' => 'Updated Name',
                'email' => $customer->email,
            ]);

            $response->assertOk()
                ->assertJsonPath('data.name', 'Updated Name');

            $this->assertDatabaseHas('customers', [
                'id' => $customer->id,
                'name' => 'Updated Name',
            ]);
        });
    });

    describe('DELETE /api/v1/customers/{customer}', function () {
        it('soft deletes a customer', function () {
            $customer = Customer::factory()->create();

            $response = $this->deleteJson("/api/v1/customers/{$customer->uuid}");

            $response->assertOk();

            $this->assertSoftDeleted('customers', [
                'id' => $customer->id,
            ]);
        });
    });

    describe('POST /api/v1/customers/{customer}/restore', function () {
        it('restores a soft deleted customer', function () {
            $customer = Customer::factory()->create();
            $customer->delete();

            $response = $this->postJson("/api/v1/customers/{$customer->uuid}/restore");

            $response->assertOk();

            $this->assertDatabaseHas('customers', [
                'id' => $customer->id,
                'deleted_at' => null,
            ]);
        });
    });

});
