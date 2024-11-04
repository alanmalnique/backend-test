<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Mock\Customer\CustomerResponseMock;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    public function test_GetExistingCustomersById_ShouldReturnSuccessfulResponse()
    {
        $customer = $this->createCustomer();

        $response = $this->get('/api/customers/' . $customer->id);

        $response->assertStatus(200);
        $response->assertJson(CustomerResponseMock::successResponse(
            $customer->id,
            $customer->name,
            $customer->surname,
            $customer->balance,
            $customer->created_at,
            $customer->updated_at
        ));
    }

    public function test_GetNotExistingCustomersById_ShouldReturnUnsuccessfulResponse()
    {
        $response = $this->get('/api/customers/' . rand());

        $response->assertStatus(404);
    }

    public static function provideCustomerData(): \Generator
    {
        yield ['name' => 'Test', 'surname' => 'Test', 'expectedHttpStatus' => 200];
        yield ['name' => '', 'surname' => 'Test', 'expectedHttpStatus' => 422];
        yield ['name' => 'Test', 'surname' => '', 'expectedHttpStatus' => 422];
        yield ['name' => '', 'surname' => '', 'expectedHttpStatus' => 422];
        yield ['name' => 'Name Test with more than max chars', 'surname' => 'Test', 'expectedHttpStatus' => 422];
        yield ['name' => 'Test', 'surname' => 'Surname test with more than max chars to throw a exception', 'expectedHttpStatus' => 422];
    }

    #[DataProvider('provideCustomerData')] public function test_CreateCustomerWithProvidedData_ShouldReturnProvidedExpectedHttpStatus(
        string $name,
        string $surname,
        int $expectedHttpStatus
    ){
        $response = $this->postJson('/api/customers', [
            'name' => $name,
            'surname' => $surname,
        ]);

        $response->assertStatus($expectedHttpStatus);
    }

    public function test_CreateCustomerWithCorrectData_ShouldCreateCustomer()
    {
        $response = $this->postJson('/api/customers', [
            'name' => 'Test',
            'surname' => 'Test',
        ]);
        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $response->assertJson(CustomerResponseMock::successResponse(
            $responseData['id'],
            $responseData['name'],
            $responseData['surname'],
            $responseData['balance'],
            $responseData['created_at'],
            $responseData['updated_at']
        ));
    }

    #[DataProvider('provideCustomerData')] public function test_UpdateCustomerWithProvidedData_ShouldReturnProvidedExpectedHttpStatus(
        string $name,
        string $surname,
        int $expectedHttpStatus
    ){
        $customer = $this->createCustomer();

        $response = $this->putJson('/api/customers/' . $customer->id, [
            'name' => $name,
            'surname' => $surname,
        ]);

        $response->assertStatus($expectedHttpStatus);
    }

    public function test_UpdateCustomerWithCorrectData_ShouldCreateCustomer()
    {
        $customer = $this->createCustomer();

        $response = $this->putJson('/api/customers/' . $customer->id, [
            'name' => 'Test',
            'surname' => 'Test',
        ]);
        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $response->assertJson(CustomerResponseMock::successResponse(
            $responseData['id'],
            $responseData['name'],
            $responseData['surname'],
            $responseData['balance'],
            $responseData['created_at'],
            $responseData['updated_at']
        ));
    }

    public function test_DeleteExistingCustomer_ShouldReturnSuccessfulResponse()
    {
        $customer = $this->createCustomer();

        $response = $this->deleteJson('/api/customers/' . $customer->id);

        $response->assertStatus(200);
    }

    public function test_DeleteInvalidCustomer_ShouldReturnSuccessfulResponse()
    {
        $response = $this->deleteJson('/api/customers/' . rand());

        $response->assertStatus(404);
    }
}
