<?php

declare(strict_types=1);

namespace PasswordValidator\Constraints;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PasswordValidator extends ConstraintValidator
{
    /** @var string|null */
    private $plainPasswordProperty;

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Password) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\Password');
        }

        if ($value instanceof UserInterface) {
            $plainPasswordAccessor = $constraint->plainPasswordAccessor;
            $this->plainPasswordProperty = $constraint->plainPasswordProperty;

            if (null === $plainPasswordAccessor || null === $this->plainPasswordProperty) {
                throw new MissingOptionsException('The plainPasswordAccessor and plainPasswordProperty options are required when using the class constraint.', ['plainPasswordAccessor', 'plainPasswordProperty']);
            }

            $stringValue = (string)$value->$plainPasswordAccessor();
        } else {
            $stringValue = (string)$value;
        }

        $length = mb_strlen($stringValue);

        if (null !== $constraint->max && $length > $constraint->max) {
            $this->buildViolation(
                $value,
                $constraint->min === $constraint->max ? $constraint->exactMessage : $constraint->maxMessage,
                ['{{ value }}' => $this->formatValue($stringValue), '{{ limit }}' => $constraint->max]
            )
                ->setPlural((int)$constraint->max)
                ->setCode(Password::TOO_LONG_ERROR)
                ->addViolation();
        }

        if (null !== $constraint->min && $length < $constraint->min) {
            $this->buildViolation($value,
                $constraint->min === $constraint->max ? $constraint->exactMessage : $constraint->minMessage,
                ['{{ value }}' => $this->formatValue($stringValue), '{{ limit }}' => $constraint->min]
            )
                ->setPlural((int)$constraint->min)
                ->setCode(Password::TOO_SHORT_ERROR)
                ->addViolation();
        }

        if ($value instanceof UserInterface && false !== strpos($stringValue, $value->getUserIdentifier())) {
            $this->context
                ->buildViolation($constraint->usernameMessage)
                ->setInvalidValue($value)
                ->atPath($this->plainPasswordProperty)
                ->addViolation();
        }

        // Check whether there is at least one upper cased character present
        if (!\preg_match('/[A-Z]/', $stringValue)) {
            $this->buildViolation($value, $constraint->upperCaseCharacterMissingMessage)->addViolation();
        }

        // Check whether there is at least one lower cased character present
        if (!\preg_match('/[a-z]/', $stringValue)) {
            $this->buildViolation($value, $constraint->lowerCaseCharacterMissingMessage)->addViolation();
        }

        // Check whether there is at least one number present
        if (!\preg_match('/[0-9]/', $stringValue)) {
            $this->buildViolation($value, $constraint->numberMissingMessage)->addViolation();
        }
    }

    private function buildViolation($value, string $message, array $parameters = [])
    {
        $isClassConstraint = $value instanceof UserInterface;

        $violation = $this->context->buildViolation($message);
        $violation->setInvalidValue($value);
        foreach ($parameters as $parameterKey => $parameter) {
            $violation->setParameter($parameterKey, (string)$parameter);
        }

        if ($isClassConstraint) {
            $violation->atPath($this->plainPasswordProperty);
        }

        return $violation;
    }
}
