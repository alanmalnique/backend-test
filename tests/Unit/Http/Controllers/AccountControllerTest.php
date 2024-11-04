<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\AccountController;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\TransferRequest;
use App\Http\Requests\WithdrawRequest;
use App\Repositories\CustomerRepository;
use Illuminate\Http\Exceptions\HttpResponseException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    private AccountController $accountController;

    public function setUp(): void
    {
        parent::setUp();
        $customerRepository = new CustomerRepository();
        $this->accountController = new AccountController($customerRepository);
    }

    public static function successBalanceDataProvider(): \Generator {
        yield ['balance' => 0];
        yield ['balance' => 50];
        yield ['balance' => 100];
    }

    #[DataProvider('successBalanceDataProvider')] public function test_WhenTryToGetBalanceFromExistingCustomer_ShouldReturnSuccessfulResponse(
        float $balance
    ): void
    {
        $customer = $this->createCustomer(
            'Customer',
            'Test',
            $balance
        );

        $response = $this->accountController->getBalance($customer->id);
        $this->assertJson($response->content());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_WhenTryToGetBalanceFromInvalidCustomer_ShouldReturnNotFoundHttpStatusCode(): void
    {
        $response = $this->accountController->getBalance(rand());

        $this->assertEquals(404, $response->getStatusCode());
    }

    public static function successDepositDataProvider(): \Generator {
        yield ['funds' => 50, 'balance' => 0];
        yield ['funds' => 50, 'balance' => 50];
    }

    #[DataProvider('successDepositDataProvider')] public function test_WhenTryToMakeDepositFromExistingCustomer_ShouldReturnSuccessfulResponse(
        float $funds,
        float $balance
    ): void
    {
        $customer = $this->createCustomer(
            'Customer',
            'Test',
            $balance
        );

        $depositData = $this->createDepositRequest($funds);

        $response = $this->accountController->deposit($depositData, $customer->id);
        $this->assertJson($response->content());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_WhenTryToMakeDepositToInvalidCustomer_ShouldReturnNotFoundHttpStatusCode(): void
    {
        $depositData = $this->createDepositRequest(50);

        $response = $this->accountController->deposit($depositData, rand());

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_WhenTryToMakeDepositWithInvalidFunds_ShouldReturnValidationErrors(): void
    {
        $this->expectException(HttpResponseException::class);
        $depositData = $this->createDepositRequest('invalid');

        $this->setContainerToValidator($depositData);

        $depositData->validateResolved();
    }

    public static function successWithdrawDataProvider(): \Generator {
        yield ['funds' => 50, 'balance' => 50];
        yield ['funds' => 50, 'balance' => 100];
    }

    #[DataProvider('successWithdrawDataProvider')] public function test_WhenTryToMakeWithdrawFromExistingCustomer_ShouldReturnSuccessfulResponse(
        float $funds,
        float $balance
    ): void
    {
        $customer = $this->createCustomer(
            'Customer',
            'Test',
            $balance
        );

        $withdrawData = $this->createWithdrawRequest($funds);

        $response = $this->accountController->withdraw($withdrawData, $customer->id);
        $this->assertJson($response->content());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public static function failWithdrawDataProvider(): \Generator {
        yield ['funds' => 100, 'balance' => 50];
        yield ['funds' => 50.01, 'balance' => 50];
    }

    #[DataProvider('failWithdrawDataProvider')] public function test_WhenTryToMakeWithdrawFromExistingCustomerAndInvalidProvidedData_ShouldReturnErrorResponse(
        float $funds,
        float $balance
    ): void
    {
        $customer = $this->createCustomer(
            'Customer',
            'Test',
            $balance
        );

        $withdrawData = $this->createWithdrawRequest($funds);

        $response = $this->accountController->withdraw($withdrawData, $customer->id);
        $this->assertJson($response->content());
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_WhenTryToWithdrawToInvalidCustomer_ShouldReturnNotFoundHttpStatusCode(): void
    {
        $withdrawData = $this->createWithdrawRequest(50);

        $response = $this->accountController->withdraw($withdrawData, rand());

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_WhenTryToWithdrawWithInvalidFunds_ShouldReturnValidationErrors(): void
    {
        $this->expectException(HttpResponseException::class);
        $withdrawData = $this->createWithdrawRequest('invalid');

        $this->setContainerToValidator($withdrawData);

        $withdrawData->validateResolved();
    }

    public static function provideSuccessTransferData(): \Generator
    {
        yield ['balanceFrom' => 100, 'balanceTo' => 0, 'funds' => 50];
        yield ['balanceFrom' => 50, 'balanceTo' => 0, 'funds' => 50];
    }

    #[DataProvider('provideSuccessTransferData')] public function test_MakeTransferWithProvidedData_ShouldReturnSuccessfulResponse(
        float $balanceFrom,
        float $balanceTo,
        float $funds,
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

        $transferData = $this->createTransferRequest($customerFrom->id, $customerTo->id, $funds);

        $response = $this->accountController->transfer($transferData);
        $this->assertJson($response->content());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public static function provideFailTransferData(): \Generator
    {
        yield ['balance' => 50, 'funds' => 100];
        yield ['balance' => 50, 'funds' => 50.01];
    }

    #[DataProvider('provideFailTransferData')] public function test_MakeTransferWithInvalidProvidedData_ShouldReturnErrorResponse(
        float $balance,
        float $funds,
    ){
        $customerFrom = $this->createCustomer(
            'Test',
            'From',
            $balance
        );

        $customerTo = $this->createCustomer(
            'Test',
            'To'
        );

        $transferData = $this->createTransferRequest($customerFrom->id, $customerTo->id, $funds);

        $response = $this->accountController->transfer($transferData);
        $this->assertJson($response->content());
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_WhenTryToTransferToInvalidCustomer_ShouldReturnNotFoundHttpStatusCode(): void
    {
        $transferData = $this->createTransferRequest('invalid', 1, 50);

        $response = $this->accountController->transfer($transferData);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_WhenTryToTransferWithInvalidData_ShouldReturnValidationErrors(): void
    {
        $this->expectException(HttpResponseException::class);
        $transferData = $this->createTransferRequest('invalid', 'invalid', 'invalid');

        $this->setContainerToValidator($transferData);

        $transferData->validateResolved();
    }

    public function test_WhenTryToTransferWithInvalidCustomerFrom_ShouldReturnValidationErrors(): void
    {
        $customerTo = $this->createCustomer(
            'Test',
            'To'
        );

        $this->expectException(HttpResponseException::class);
        $transferData = $this->createTransferRequest('invalid', $customerTo->id, 50);

        $this->setContainerToValidator($transferData);

        $transferData->validateResolved();
    }

    public function test_WhenTryToTransferWithInvalidCustomerTo_ShouldReturnValidationErrors(): void
    {
        $customerFrom = $this->createCustomer(
            'Test',
            'To'
        );

        $this->expectException(HttpResponseException::class);
        $transferData = $this->createTransferRequest($customerFrom->id, 'invalid', 50);

        $this->setContainerToValidator($transferData);

        $transferData->validateResolved();
    }

    public function test_WhenTryToTransferWithInvalidFunds_ShouldReturnValidationErrors(): void
    {
        $customerFrom = $this->createCustomer(
            'Test',
            'To'
        );
        $customerTo = $this->createCustomer(
            'Test',
            'To'
        );

        $this->expectException(HttpResponseException::class);
        $transferData = $this->createTransferRequest($customerFrom->id, $customerTo->id, 'invalid');

        $this->setContainerToValidator($transferData);

        $transferData->validateResolved();
    }

    private function createDepositRequest(mixed $funds): DepositRequest
    {
        return DepositRequest::create('/accounts/{id}/deposit', 'POST', [
            'funds' => $funds
        ]);
    }

    private function createWithdrawRequest(mixed $funds): WithdrawRequest
    {
        return WithdrawRequest::create('/accounts/{id}/withdraw', 'POST', [
            'funds' => $funds
        ]);
    }

    private function createTransferRequest(mixed $from, mixed $to, mixed $funds): TransferRequest
    {
        return TransferRequest::create('/accounts/transfer', 'POST', [
            'from' => $from,
            'to' => $to,
            'funds' => $funds
        ]);
    }
}
