<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Mock\Customer\AccountResponseMock;
use Tests\TestCase;

class AccountTest extends TestCase
{
    public function test_GetBalanceFromExistingCustomer_ShouldReturnSuccessfulResponse()
    {
        $customer = $this->createCustomer();

        $response = $this->get('/api/accounts/' . $customer->id);

        $response->assertStatus(200);
        $response->assertJson(AccountResponseMock::getBalanceSuccessResponse(
            $customer->balance,
        ));
    }

    public function test_GetBalanceFromInvalidCustomer_ShouldReturnErrorResponse()
    {
        $response = $this->get('/api/accounts/' . rand());

        $response->assertStatus(404);
    }

    public static function provideDepositData(): \Generator
    {
        yield ['funds' => 50, 'expectedBalance' => 50];
        yield ['funds' => 100, 'expectedBalance' => 100];
    }

    #[DataProvider('provideDepositData')] public function test_MakeDepositWithProvidedData_ShouldReturnExpectedHttpStatusAndBalance(
        float $funds,
        float $expectedBalance
    ){
        $customer = $this->createCustomer();

        $response = $this->postJson('/api/accounts/' . $customer->id . '/deposit', [
            'funds' => $funds
        ]);

        $response->assertStatus(200);
        $response->assertJson(AccountResponseMock::getMessageResponse('Deposit successful'));

        $response = $this->get('/api/accounts/' . $customer->id);

        $response->assertStatus(200);
        $response->assertJson(AccountResponseMock::getBalanceSuccessResponse(
            $expectedBalance,
        ));
    }

    public function test_MakeDepositWithInvalidCustomer_ShouldReturnErrorResponse()
    {
        $response = $this->postJson('/api/accounts/' . rand() . '/deposit', [
            'funds' => rand()
        ]);

        $response->assertStatus(404);
    }

    public function test_MakeDepositWithInvalidFunds_ShouldReturnErrorResponse()
    {
        $response = $this->postJson('/api/accounts/' . rand() . '/deposit', [
            'funds' => 'invalid'
        ]);

        $response->assertStatus(422);
    }

    public static function provideWithdrawData(): \Generator
    {
        yield ['balance' => 100, 'funds' => 50, 'expectedBalance' => 50];
        yield ['balance' => 100, 'funds' => 100, 'expectedBalance' => 0];
    }

    #[DataProvider('provideWithdrawData')] public function test_MakeWithdrawWithProvidedData_ShouldReturnExpectedHttpStatusAndBalance(
        float $balance,
        float $funds,
        float $expectedBalance
    ){
        $customer = $this->createCustomer(
            'Test',
            'Test',
            $balance
        );

        $response = $this->postJson('/api/accounts/' . $customer->id . '/withdraw', [
            'funds' => $funds
        ]);

        $response->assertStatus(200);
        $response->assertJson(AccountResponseMock::getMessageResponse('Withdrawal successful'));

        $response = $this->get('/api/accounts/' . $customer->id);

        $response->assertStatus(200);
        $response->assertJson(AccountResponseMock::getBalanceSuccessResponse(
            $expectedBalance,
        ));
    }

    public function test_MakeWithdrawWithFundBiggerThanBalance_ShouldReturnError(){
        $customer = $this->createCustomer(
            'Test',
            'Test',
            50
        );

        $response = $this->postJson('/api/accounts/' . $customer->id . '/withdraw', [
            'funds' => 100
        ]);

        $response->assertStatus(400);
        $response->assertJson(AccountResponseMock::getMessageResponse('Insufficient funds'));
    }

    public function test_MakeWithdrawWithInvalidCustomer_ShouldReturnErrorResponse()
    {
        $response = $this->postJson('/api/accounts/' . rand() . '/deposit', [
            'funds' => rand()
        ]);

        $response->assertStatus(404);
    }

    public function test_MakeWithdrawWithInvalidFunds_ShouldReturnErrorResponse()
    {
        $response = $this->postJson('/api/accounts/' . rand() . '/withdraw', [
            'funds' => 'invalid'
        ]);

        $response->assertStatus(422);
    }

    public static function provideTransferData(): \Generator
    {
        yield ['balanceFrom' => 100, 'balanceTo' => 0, 'funds' => 50, 'expectedBalanceFrom' => 50, 'expectedBalanceTo' => 50];
        yield ['balanceFrom' => 50, 'balanceTo' => 0, 'funds' => 50, 'expectedBalanceFrom' => 0, 'expectedBalanceTo' => 50];
    }

    #[DataProvider('provideTransferData')] public function test_MakeTransferWithProvidedData_ShouldReturnExpectedHttpStatusAndBalance(
        float $balanceFrom,
        float $balanceTo,
        float $funds,
        float $expectedBalanceFrom,
        float $expectedBalanceTo
    ){
        $customerFrom = $this->createCustomer(
            'Test',
            'From',
            $balanceFrom
        );

        $customerTo = $this->createCustomer(
            'Test',
            'To',
            $balanceTo
        );

        $response = $this->postJson('/api/accounts/transfer', [
            'from' => $customerFrom->id,
            'to' => $customerTo->id,
            'funds' => $funds
        ]);

        $response->assertStatus(200);
        $response->assertJson(AccountResponseMock::getMessageResponse('Transfer successful'));

        $responseFrom = $this->get('/api/accounts/' . $customerFrom->id);

        $responseFrom->assertStatus(200);
        $responseFrom->assertJson(AccountResponseMock::getBalanceSuccessResponse(
            $expectedBalanceFrom,
        ));

        $responseTo = $this->get('/api/accounts/' . $customerTo->id);

        $responseTo->assertStatus(200);
        $responseTo->assertJson(AccountResponseMock::getBalanceSuccessResponse(
            $expectedBalanceTo,
        ));
    }

    public static function provideInvalidTransferData(): \Generator
    {
        yield ['from' => 1, 'to' => null, 'funds' => 50];
        yield ['from' => null, 'to' => 1, 'funds' => 50];
        yield ['from' => 1, 'to' => 1, 'funds' => null];
        yield ['from' => 'invalid', 'to' => 1, 'funds' => 50];
        yield ['from' => 1, 'to' => 'invalid', 'funds' => 50];
        yield ['from' => 1, 'to' => 1, 'funds' => 'invalid'];
    }

    #[DataProvider('provideInvalidTransferData')] public function test_MakeTransferWithInvalidData_ShouldReturnErrorResponse(
        mixed $from,
        mixed $to,
        mixed $funds
    ){
        $response = $this->postJson('/api/accounts/transfer', [
            'from' => $from,
            'to' => $to,
            'funds' => $funds
        ]);

        $response->assertStatus(422);
    }

    public function test_MakeTransferWithInvalidCustomerFromAndTo_ShouldReturnErrorResponse()
    {
        $response = $this->postJson('/api/accounts/transfer', [
            'from' => rand(),
            'to' => rand(),
            'funds' => 10
        ]);

        $response->assertStatus(422);
        $response->assertJson(AccountResponseMock::getErrorsFromAndToResponse());
    }

    public function test_MakeTransferWithInvalidCustomerFrom_ShouldReturnErrorResponse()
    {
        $customerTo = $this->createCustomer(
            'Test',
            'To',
            0
        );

        $response = $this->postJson('/api/accounts/transfer', [
            'from' => rand(),
            'to' => $customerTo->id,
            'funds' => 10
        ]);

        $response->assertStatus(422);
        $response->assertJson(AccountResponseMock::getErrorsFromAndToResponse(true, false));
    }

    public function test_MakeTransferWithInvalidCustomerTo_ShouldReturnErrorResponse()
    {
        $customerFrom = $this->createCustomer(
            'Test',
            'From',
            0
        );

        $response = $this->postJson('/api/accounts/transfer', [
            'from' => $customerFrom->id,
            'to' => rand(),
            'funds' => 10
        ]);

        $response->assertStatus(422);
        $response->assertJson(AccountResponseMock::getErrorsFromAndToResponse(false));
    }
}
