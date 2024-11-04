<?php
namespace App\DTOs;

class CustomerDTO
{
    private string $name;
    private string $surname;
    private float $balance;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->surname = $data['surname'];
        $this->balance = $data['balance'] ?? 0;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }
}
