<?php
namespace App\Repositories;

use App\Exceptions\InsufficientFundsException;
use App\Models\Customer;
use App\DTOs\CustomerDTO;
use Exception;
use Illuminate\Support\Facades\DB;

class CustomerRepository
{
    /**
     * @throws Exception
     */
    public function getCustomerById(int $id): Customer
    {
        return Customer::query()->findOrFail($id);
    }

    public function createCustomer(CustomerDTO $dto): Customer
    {
        return Customer::query()->create([
            'name' => $dto->getName(),
            'surname' => $dto->getSurname(),
            'balance' => $dto->getBalance()
        ]);
    }

    public function updateCustomer(int $id, CustomerDTO $dto): Customer
    {
        $customer = Customer::query()->findOrFail($id);
        $customer->update([
            'name' => $dto->getName(),
            'surname' => $dto->getSurname(),
            'balance' => $dto->getBalance()
        ]);
        return $customer;
    }

    public function deleteCustomer(int $id): int
    {
        $customer = Customer::query()->findOrFail($id);
        return $customer->delete();
    }

    public function deposit($id, $amount): void
    {
        $customer = Customer::query()->findOrFail($id);
        $customer->balance += $amount;
        $customer->save();
    }

    /**
     * @throws Exception
     */
    public function withdraw($id, $amount): void
    {
        $customer = Customer::query()->findOrFail($id);
        if ($customer->balance < $amount) {
            throw new InsufficientFundsException("Insufficient funds");
        }
        $customer->balance -= $amount;
        $customer->save();
    }

    /**
     * @throws Exception
     * @throws InsufficientFundsException
     */
    public function transfer($fromId, $toId, $amount): void
    {
        $this->withdraw($fromId, $amount);
        $this->deposit($toId, $amount);
    }
}
