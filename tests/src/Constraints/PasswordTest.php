<?php
declare(strict_types=1);

use PasswordValidator\Constraints\Password;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\MissingOptionsException;

class PasswordTest extends TestCase
{
    public function testConstructionWithoutAccessor()
    {
        $this->expectException(MissingOptionsException::class);
        new Password(['plainPasswordProperty' => 'foo']);
    }

    public function testConstructionWithoutFieldDefinition()
    {
        $this->expectException(MissingOptionsException::class);
        new Password(['plainPasswordAccessor' => 'getFoo']);
    }

    public function testConstructionWithProperParameters()
    {
        $this->assertInstanceOf(Password::class, new Password(['plainPasswordAccessor' => 'getFoo', 'plainPasswordProperty' => 'foo']));
    }
}
