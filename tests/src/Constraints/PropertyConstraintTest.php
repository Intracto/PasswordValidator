<?php

declare(strict_types=1);

use PasswordValidator\Constraints\Password;
use PasswordValidator\Constraints\PasswordValidator;

class PropertyConstraintTest extends \Symfony\Component\Validator\Test\ConstraintValidatorTestCase
{
    public function testLength()
    {
        $password = 'Foo1';
        $passwordConstraint = new Password();

        $this->validator->validate($password, $passwordConstraint);

        $this->assertSame(1, $this->context->getViolations()->count());
        $this->assertSame($passwordConstraint->minMessage, $this->context->getViolations()->get(0)->getMessageTemplate());
    }

    public function testNumber()
    {
        $password = 'Foobarbaz@example.com';
        $passwordConstraint = new Password();
        $this->validator->validate($password, $passwordConstraint);

        $this->assertSame(1, $this->context->getViolations()->count());
        $this->assertSame($passwordConstraint->numberMissingMessage, $this->context->getViolations()->get(0)->getMessageTemplate());
    }

    public function testUpperCase()
    {
        $password = 'foobarbaz1@example.com';
        $passwordConstraint = new Password();

        $this->validator->validate($password, $passwordConstraint);

        $this->assertSame(1, $this->context->getViolations()->count());
        $this->assertSame($passwordConstraint->upperCaseCharacterMissingMessage, $this->context->getViolations()->get(0)->getMessageTemplate());
    }

    public function testLowerCase()
    {
        $password = 'FOOBARBAZ1';
        $passwordConstraint = new Password();

        $this->validator->validate($password, $passwordConstraint);

        $this->assertSame(1, $this->context->getViolations()->count());
        $this->assertSame($passwordConstraint->lowerCaseCharacterMissingMessage, $this->context->getViolations()->get(0)->getMessageTemplate());
    }

    public function testProperPassword()
    {
        $password = 'FOOBARBAZ1@example.com';
        $passwordConstraint = new Password();

        $this->validator->validate($password, $passwordConstraint);
        $this->assertNoViolation();
    }

    // Check whether class and property options are mixed
    public function testInvalidPropertyConfiguration()
    {
        $password = 'FOOBARBAZ1@example.com';
        $passwordConstraint = new Password(['plainPasswordAccessor' => 'getPassword', 'plainPasswordProperty' => 'password']);

        $this->validator->validate($password, $passwordConstraint);
        $this->assertNoViolation();
    }

    protected function createValidator()
    {
        return new PasswordValidator();
    }
}
