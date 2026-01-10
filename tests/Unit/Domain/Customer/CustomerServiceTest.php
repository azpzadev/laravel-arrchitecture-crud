<?php

declare(strict_types=1);

use App\Domain\Customer\DTOs\CustomerData;
use App\Domain\Customer\DTOs\CustomerFilterData;
use App\Domain\Customer\Enums\CustomerStatus;
use App\Domain\Customer\Exceptions\CustomerAlreadyExistsException;
use App\Domain\Customer\Exceptions\CustomerNotFoundException;
use App\Domain\Customer\Services\CustomerService;
use App\Infrastructure\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('CustomerService', function () {

    beforeEach(function () {
        $this->service = app(CustomerService::class);
    });

    describe('create', function () {
        it('creates a customer with valid data', function () {
            $data = CustomerData::fromArray([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '1234567890',
            ]);

            $customer = $this->service->create($data);

            expect($customer)->toBeInstanceOf(Customer::class);
            expect($customer->name)->toBe('John Doe');
            expect($customer->email)->toBe('john@example.com');
            expect($customer->status)->toBe(CustomerStatus::Active);
        });

        it('throws exception for duplicate email', function () {
            Customer::factory()->create(['email' => 'existing@example.com']);

            $data = CustomerData::fromArray([
                'name' => 'New Customer',
                'email' => 'existing@example.com',
            ]);

            $this->service->create($data);
        })->throws(CustomerAlreadyExistsException::class);
    });

    describe('find', function () {
        it('finds customer by id', function () {
            $customer = Customer::factory()->create();

            $found = $this->service->find($customer->id);

            expect($found->id)->toBe($customer->id);
        });

        it('throws exception for non-existent id', function () {
            $this->service->find(99999);
        })->throws(CustomerNotFoundException::class);
    });

    describe('findByUuid', function () {
        it('finds customer by uuid', function () {
            $customer = Customer::factory()->create();

            $found = $this->service->findByUuid($customer->uuid);

            expect($found->uuid)->toBe($customer->uuid);
        });
    });

    describe('update', function () {
        it('updates customer with valid data', function () {
            $customer = Customer::factory()->create();

            $data = CustomerData::fromArray([
                'name' => 'Updated Name',
                'email' => $customer->email,
                'status' => CustomerStatus::Inactive->value,
            ]);

            $updated = $this->service->update($customer, $data);

            expect($updated->name)->toBe('Updated Name');
            expect($updated->status)->toBe(CustomerStatus::Inactive);
        });
    });

    describe('delete', function () {
        it('soft deletes customer', function () {
            $customer = Customer::factory()->create();

            $result = $this->service->delete($customer);

            expect($result)->toBeTrue();
            expect($customer->fresh()->deleted_at)->not->toBeNull();
        });

        it('force deletes customer', function () {
            $customer = Customer::factory()->create();

            $result = $this->service->delete($customer, force: true);

            expect($result)->toBeTrue();
            expect(Customer::withTrashed()->find($customer->id))->toBeNull();
        });
    });

    describe('paginate', function () {
        it('returns paginated customers', function () {
            Customer::factory()->count(20)->create();

            $filters = CustomerFilterData::fromArray(['per_page' => 10]);
            $result = $this->service->paginate($filters);

            expect($result->count())->toBe(10);
            expect($result->total())->toBe(20);
        });

        it('filters by status', function () {
            Customer::factory()->active()->count(5)->create();
            Customer::factory()->inactive()->count(3)->create();

            $filters = CustomerFilterData::fromArray(['status' => 'active']);
            $result = $this->service->paginate($filters);

            expect($result->total())->toBe(5);
        });
    });

});
