<?php

declare(strict_types=1);

namespace App\Validation;

class UserValidator
{
    public function validateEmail(?string $email): bool
    {
        if (empty($email)) {
            return false;
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function validatePassword(?string $password): bool
    {
        if (empty($password)) {
            return false;
        }

        return true;
    }
}
