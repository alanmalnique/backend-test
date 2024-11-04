<?php
namespace App\Http\Controllers;

use App\DTOs\CustomerDTO;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Repositories\CustomerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected CustomerRepository $repository;

    public function __construct(CustomerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function show(Request $request, mixed $id): JsonResponse
    {
        try {
            $customer = $this->repository->getCustomerById($id);
            return response()->json($customer);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customerDTO = new CustomerDTO($request->validated());
        return response()->json($this->repository->createCustomer($customerDTO));
    }

    public function update(UpdateCustomerRequest $request, $id): JsonResponse
    {
        $customerDTO = new CustomerDTO($request->validated());
        return response()->json($this->repository->updateCustomer($id, $customerDTO));
    }

    public function destroy($id): JsonResponse
    {
        try {
            $customer = $this->repository->deleteCustomer($id);
            return response()->json($customer);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
    }
}
