<?php

namespace Tests\Mock\Customer;

use Carbon\Carbon;

class CustomerResponseMock
{
    public static function successResponse(
        int $id,
        string $name,
        string $surname,
        float $balance,
        string $createdAt,
        string $updatedAt,
    ): array
    {
        return [
            'id' => $id,
            'name' => $name,
            'surname' => $surname,
            'balance' => sprintf("%.2f", $balance),
            'created_at' => Carbon::createFromDate($createdAt)->format('Y-m-d\TH:i:s.000000\Z'),
            'updated_at' => Carbon::createFromDate($updatedAt)->format('Y-m-d\TH:i:s.000000\Z'),
        ];
    }
}
