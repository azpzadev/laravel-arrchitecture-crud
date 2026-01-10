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

/**
 * Controller for customer CRUD operations.
 *
 * Handles customer listing, creation, retrieval, updating,
 * deletion, and restoration endpoints.
 */
class CustomerController extends Controller
{
    /**
     * Create a new CustomerController instance.
     *
     * @param CustomerService $customerService The customer service
     */
    public function __construct(
        private CustomerService $customerService
    ) {}

    /**
     * List customers with optional filtering and pagination.
     *
     * @param IndexCustomerRequest $request The validated request
     * @return JsonResponse Paginated list of customers
     */
    public function index(IndexCustomerRequest $request): JsonResponse
    {
        $customers = $this->customerService->paginate($request->toDto());

        return ApiResponse::paginated(
            $customers,
            CustomerResource::class,
            'Customers retrieved successfully'
        );
    }

    /**
     * Create a new customer.
     *
     * @param StoreCustomerRequest $request The validated request
     * @return JsonResponse The created customer
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = $this->customerService->create($request->toDto());

        return ApiResponse::resource(
            new CustomerResource($customer),
            'Customer created successfully',
            201
        );
    }

    /**
     * Retrieve a single customer.
     *
     * @param Customer $customer The customer (route model binding)
     * @return JsonResponse The customer data
     */
    public function show(Customer $customer): JsonResponse
    {
        return ApiResponse::resource(
            new CustomerResource($customer),
            'Customer retrieved successfully'
        );
    }

    /**
     * Update an existing customer.
     *
     * @param UpdateCustomerRequest $request The validated request
     * @param Customer $customer The customer to update
     * @return JsonResponse The updated customer
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer = $this->customerService->update($customer, $request->toDto());

        return ApiResponse::resource(
            new CustomerResource($customer),
            'Customer updated successfully'
        );
    }

    /**
     * Soft delete a customer.
     *
     * @param Customer $customer The customer to delete
     * @return JsonResponse Success message
     */
    public function destroy(Customer $customer): JsonResponse
    {
        $this->customerService->delete($customer);

        return ApiResponse::success(message: 'Customer deleted successfully');
    }

    /**
     * Restore a soft-deleted customer.
     *
     * @param Customer $customer The customer to restore
     * @return JsonResponse The restored customer
     */
    public function restore(Customer $customer): JsonResponse
    {
        $this->customerService->restore($customer);

        return ApiResponse::resource(
            new CustomerResource($customer->fresh()),
            'Customer restored successfully'
        );
    }

    /**
     * Permanently delete a customer.
     *
     * @param Customer $customer The customer to force delete
     * @return JsonResponse Success message
     */
    public function forceDelete(Customer $customer): JsonResponse
    {
        $this->customerService->delete($customer, force: true);

        return ApiResponse::success(message: 'Customer permanently deleted');
    }
}
