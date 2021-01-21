<?php

declare(strict_types=1);

namespace PasswordValidator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Password extends Constraint
{
    const TOO_SHORT_ERROR = '9ff3fdc4-b214-49db-8718-39c315e33d45';
    const TOO_LONG_ERROR = 'd94b19cc-114f-4f44-9cc4-4138e80a87b9';

    protected static $errorNames = [
        self::TOO_SHORT_ERROR => 'TOO_SHORT_ERROR',
        self::TOO_LONG_ERROR => 'TOO_LONG_ERROR',
    ];

    public $maxMessage = 'This value is too long. It should have {{ limit }} character or less.|This value is too long. It should have {{ limit }} characters or less.';
    public $minMessage = 'This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.';
    public $usernameMessage = 'This value cannot equal the username.';
    public $upperCaseCharacterMissingMessage = 'This value should contain at least one upper cased character';
    public $lowerCaseCharacterMissingMessage = 'This value should contain at least one lower cased character';
    public $numberMissingMessage = 'This value should contain at least one number';
    public $plainPasswordAccessor = null;
    public $plainPasswordProperty = null;
    public $max;
    public $min;

    public function __construct($options = null)
    {
        if (!isset($options['min'])) {
            $options['min'] = 8;
        }

        if (!isset($options['max'])) {
            $options['max'] = PHP_INT_MAX;
        }

        parent::__construct($options);
    }

    public function getTargets(): array
    {
        return [self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT];
    }
}
