<?php

declare(strict_types=1);

use PasswordValidator\Constraints\Password;
use PasswordValidator\Constraints\PasswordValidator;
use Symfony\Component\Validator\Exception\MissingOptionsException;

class PasswordValidatorTest extends \Symfony\Component\Validator\Test\ConstraintValidatorTestCase
{
    public function testNotEqualsUserName()
    {
        $user = new Symfony\Component\Security\Core\User\User('Foobarbaz1@example.com', 'Foobarbaz1@example.com');
        $passwordConstraint = new Password(['plainPasswordAccessor' => 'getPassword', 'plainPasswordProperty' => 'password']);

        $this->validator->validate($user, $passwordConstraint);

        $this->assertSame(1, $this->context->getViolations()->count());
        $this->assertSame($passwordConstraint->usernameMessage, $this->context->getViolations()->get(0)->getMessageTemplate());
    }

    public function testNotContainsUserName()
    {
        $user = new Symfony\Component\Security\Core\User\User('Foobarbaz1@example.com', 'HelloFoobarbaz1@example.comWorld');
        $passwordConstraint = new Password(['plainPasswordAccessor' => 'getPassword', 'plainPasswordProperty' => 'password']);

        $this->validator->validate($user, $passwordConstraint);

        $this->assertSame(1, $this->context->getViolations()->count());
        $this->assertSame($passwordConstraint->usernameMessage, $this->context->getViolations()->get(0)->getMessageTemplate());
    }

    public function testLength()
    {
        $user = new Symfony\Component\Security\Core\User\User('Foobarbaz1@example.com', 'Foo1');
        $passwordConstraint = new Password(['plainPasswordAccessor' => 'getPassword', 'plainPasswordProperty' => 'password']);

        $this->validator->validate($user, $passwordConstraint);

        $this->assertSame(1, $this->context->getViolations()->count());
        $this->assertSame($passwordConstraint->minMessage, $this->context->getViolations()->get(0)->getMessageTemplate());
    }

    public function testNumber()
    {
        $user = new Symfony\Component\Security\Core\User\User('Foobarbaz1@example.com', 'Foobarbaz@example.com');
        $passwordConstraint = new Password(['plainPasswordAccessor' => 'getPassword', 'plainPasswordProperty' => 'password']);

        $this->validator->validate($user, $passwordConstraint);

        $this->assertSame(1, $this->context->getViolations()->count());
        $this->assertSame($passwordConstraint->numberMissingMessage, $this->context->getViolations()->get(0)->getMessageTemplate());
    }

    public function testUpperCase()
    {
        $user = new Symfony\Component\Security\Core\User\User('Foobarbaz1@example.com', 'foobarbaz1@example.com');
        $passwordConstraint = new Password(['plainPasswordAccessor' => 'getPassword', 'plainPasswordProperty' => 'password']);

        $this->validator->validate($user, $passwordConstraint);

        $this->assertSame(1, $this->context->getViolations()->count());
        $this->assertSame($passwordConstraint->upperCaseCharacterMissingMessage, $this->context->getViolations()->get(0)->getMessageTemplate());
    }

    public function testLowerCase()
    {
        $user = new Symfony\Component\Security\Core\User\User('Foobarbaz1@example.com', 'FOOBARBAZ1');
        $passwordConstraint = new Password(['plainPasswordAccessor' => 'getPassword', 'plainPasswordProperty' => 'password']);

        $this->validator->validate($user, $passwordConstraint);

        $this->assertSame(1, $this->context->getViolations()->count());
        $this->assertSame($passwordConstraint->lowerCaseCharacterMissingMessage, $this->context->getViolations()->get(0)->getMessageTemplate());
    }

    public function testProperPassword()
    {
        $user = new Symfony\Component\Security\Core\User\User('Foobarbaz1@example.com', 'FOOBARBAZ1@example.com');
        $passwordConstraint = new Password(['plainPasswordAccessor' => 'getPassword', 'plainPasswordProperty' => 'password']);

        $this->validator->validate($user, $passwordConstraint);
        $this->assertNoViolation();
    }

    // Check whether class and property options are mixed
    public function testInvalidUserConfiguration()
    {
        $user = new Symfony\Component\Security\Core\User\User('Foobarbaz1@example.com', 'FOOBARBAZ1@example.com');
        $passwordConstraint = new Password();

        $this->expectException(MissingOptionsException::class);
        $this->validator->validate($user, $passwordConstraint);
    }

    protected function createValidator()
    {
        return new PasswordValidator();
    }
}
