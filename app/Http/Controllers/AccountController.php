<?php
namespace App\Http\Controllers;

use App\Exceptions\InsufficientFundsException;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\TransferRequest;
use App\Http\Requests\WithdrawRequest;
use App\Repositories\CustomerRepository;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    protected CustomerRepository $repository;

    public function __construct(CustomerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getBalance($id): JsonResponse
    {
        try {
            $customer = $this->repository->getCustomerById($id);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 404);
        }
        return response()->json(['balance' => $customer->balance]);
    }

    public function deposit(DepositRequest $request, $id): JsonResponse
    {
        try {
            $this->repository->deposit($id, $request->input('funds'));
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 404);
        }
        return response()->json(['message' => 'Deposit successful']);
    }

    public function withdraw(WithdrawRequest $request, $id): JsonResponse
    {
        try {
            $this->repository->withdraw($id, $request->input('funds'));
        } catch (InsufficientFundsException $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
        return response()->json(['message' => 'Withdrawal successful']);
    }

    public function transfer(TransferRequest $request): JsonResponse
    {
        try {
            $this->repository->transfer($request->input('from'), $request->input('to'), $request->input('funds'));
        } catch (InsufficientFundsException $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
        return response()->json(['message' => 'Transfer successful']);
    }
}
