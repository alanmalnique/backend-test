<?php

namespace Tests\Mock\Customer;

use Carbon\Carbon;

class AccountResponseMock
{
    public static function getBalanceSuccessResponse(
        float $balance,
    ): array
    {
        return [
            'balance' => sprintf("%.2f", $balance),
        ];
    }

    public static function getMessageResponse(
        string $message,
    ): array
    {
        return [
            'message' => $message,
        ];
    }

    public static function getErrorResponse(
        string $message,
    ): array
    {
        return [
            'error' => $message,
        ];
    }

    public static function getErrorsFromAndToResponse(
        bool $from = true,
        bool $to = true,
    ): array
    {
        $response = ['errors' => []];
        if ($from) {
            $response['errors']['from'] = ['The selected from is invalid.'];
        }
        if ($to) {
            $response['errors']['to'] = ['The selected to is invalid.'];
        }

        return $response;
    }
}
