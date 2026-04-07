<?php

namespace App\Domain\Entities;

class Student
{
    private string $idCard;
    private string $firstName;
    private string $lastName;
    private string $address;
    private string $phone;
    private string $gender;

    public function __construct(string $idCard, string $firstName, string $lastName, string $address, string $phone, string $gender)
    {
        $this->idCard = $idCard;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->address = $address;
        $this->phone = $phone;
        $this->gender = $gender;
    }

    public function getIdCard(): string
    {
        return $this->idCard;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getGender(): string
    {
        return $this->gender;
    }
}
