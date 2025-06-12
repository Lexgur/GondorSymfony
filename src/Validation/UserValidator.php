<?php

declare(strict_types=1);

namespace App\Validation;

class UserValidator
{
    public function validateEmail(string $email): bool
    {
        if  (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }

    public function validatePassword(string $password): bool
    {
        if (empty($password)) {
            return false;
        }

        return true;
    }
}
