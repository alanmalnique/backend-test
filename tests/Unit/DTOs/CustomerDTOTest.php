<?php

namespace Tests\Unit\DTOs;

use App\DTOs\CustomerDTO;
use Tests\TestCase;

class CustomerDTOTest extends TestCase
{
    public function test_WhenProvideDataToCustomerDTO_ShouldReturnSpecifiedData()
    {
        $customerData = [
            'name' => 'Test',
            'surname' => 'Test',
            'balance' => 50
        ];

        $customerDTO = new CustomerDTO($customerData);

        $this->assertEquals($customerData['name'], $customerDTO->getName());
        $this->assertEquals($customerData['surname'], $customerDTO->getSurname());
        $this->assertEquals($customerData['balance'], $customerDTO->getBalance());
    }
}
