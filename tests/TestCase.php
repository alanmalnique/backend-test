<?php

namespace Tests;

use App\Models\Customer;
use Database\Factories\CustomerFactory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
    }

    public function createCustomer(string $name = 'Test', string $surname = 'Test', float $balance = 0): Customer
    {
        return CustomerFactory::new()->create([
            'name' => $name,
            'surname' => $surname,
            'balance' => $balance
        ]);
    }

    public function setContainerToValidator(FormRequest $request): void
    {
        $request->setContainer($this->app);
        $request->setRedirector($this->app->make('Illuminate\Routing\Redirector'));
    }

    public function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }
}
