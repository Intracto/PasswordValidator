<?php

declare(strict_types=1);

use PasswordValidator\Constraints\Password;
use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase
{
    public function testConstructionWithProperParameters()
    {
        $this->assertInstanceOf(Password::class, new Password(['plainPasswordAccessor' => 'getFoo', 'plainPasswordProperty' => 'foo']));
    }
}
