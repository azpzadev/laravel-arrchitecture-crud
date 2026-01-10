<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domain\Customer\Services\CustomerService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\IndexCustomerRequest;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Responses\ApiResponse;
use App\Infrastructure\Models\Customer;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerService $customerService
    ) {}

    public function index(IndexCustomerRequest $request): JsonResponse
    {
        $customers = $this->customerService->paginate($request->toDto());

        return ApiResponse::paginated(
            $customers,
            CustomerResource::class,
            'Customers retrieved successfully'
        );
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = $this->customerService->create($request->toDto());

        return ApiResponse::resource(
            new CustomerResource($customer),
            'Customer created successfully',
            201
        );
    }

    public function show(Customer $customer): JsonResponse
    {
        return ApiResponse::resource(
            new CustomerResource($customer),
            'Customer retrieved successfully'
        );
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer = $this->customerService->update($customer, $request->toDto());

        return ApiResponse::resource(
            new CustomerResource($customer),
            'Customer updated successfully'
        );
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $this->customerService->delete($customer);

        return ApiResponse::success(message: 'Customer deleted successfully');
    }

    public function restore(Customer $customer): JsonResponse
    {
        $this->customerService->restore($customer);

        return ApiResponse::resource(
            new CustomerResource($customer->fresh()),
            'Customer restored successfully'
        );
    }

    public function forceDelete(Customer $customer): JsonResponse
    {
        $this->customerService->delete($customer, force: true);

        return ApiResponse::success(message: 'Customer permanently deleted');
    }
}
