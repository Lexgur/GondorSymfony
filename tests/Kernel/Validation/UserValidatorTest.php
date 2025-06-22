<?php

declare(strict_types=1);

namespace App\Tests\Kernel\Validation;

use App\Validation\UserValidator;
use PHPUnit\Framework\TestCase;

class UserValidatorTest extends TestCase
{
    private UserValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new UserValidator();
    }

    public function testEmailValidatesWhenACorrectPatternIsGiven(): void
    {
        $email = 'test@test.com';

        $this->assertTrue($this->validator->validateEmail($email));
    }

    public function testEmailValidatesWhenACorrectPatternIsNotValid(): void
    {
        $email = 'testWrongEmailPattern';

        $this->assertFalse($this->validator->validateEmail($email));
    }

    public function testPasswordValidationReturnsTrueWhenAnyStringIsGiven(): void
    {
        $this->assertTrue($this->validator->validatePassword('test'));
    }

    public function testPasswordValidationReturnsFalseWhenAnEmptyStringIsGiven(): void
    {
        $this->assertFalse($this->validator->validatePassword(''));
    }
}
