<?php
declare(strict_types=1);

namespace PasswordValidator\Constraints;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class PasswordValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Password) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\Password');
        }

        if (!($value instanceof UserInterface)) {
            throw new UnexpectedTypeException($value, UserInterface::class);
        }

        $plainPasswordAccessor = $constraint->plainPasswordAccessor;
        $plainPasswordProperty = $constraint->plainPasswordProperty;

        $stringValue = (string) $value->$plainPasswordAccessor();

        $length = mb_strlen($stringValue);

        if (null !== $constraint->max && $length > $constraint->max) {
            $this->context->buildViolation($constraint->min == $constraint->max ? $constraint->exactMessage : $constraint->maxMessage)
                ->setParameter('{{ value }}', $this->formatValue($stringValue))
                ->setParameter('{{ limit }}', $constraint->max)
                ->setInvalidValue($value)
                ->setPlural((int)$constraint->max)
                ->atPath($plainPasswordProperty)
                ->setCode(Password::TOO_LONG_ERROR)
                ->addViolation();
        }

        if (null !== $constraint->min && $length < $constraint->min) {
            $this->context->buildViolation($constraint->min == $constraint->max ? $constraint->exactMessage : $constraint->minMessage)
                ->setParameter('{{ value }}', $this->formatValue($stringValue))
                ->setParameter('{{ limit }}', $constraint->min)
                ->setInvalidValue($value)
                ->atPath($plainPasswordProperty)
                ->setPlural((int)$constraint->min)
                ->setCode(Password::TOO_SHORT_ERROR)
                ->addViolation();
        }

        if (null !== $plainPasswordAccessor && $value->getUserName() === $stringValue) {
            $this->context
                ->buildViolation($constraint->usernameMessage)
                ->setInvalidValue($value)
                ->atPath($plainPasswordProperty)
                ->addViolation();
        }

        // Check whether there is at least one upper cased character present
        if (!\preg_match('/[A-Z]/', $stringValue)) {
            $this->context
                ->buildViolation($constraint->upperCaseCharacterMissingMessage)
                ->setInvalidValue($value)
                ->atPath($plainPasswordProperty)
                ->addViolation();
        }

        // Check whether there is at least one lower cased character present
        if (!\preg_match('/[a-z]/', $stringValue)) {
            $this->context
                ->buildViolation($constraint->lowerCaseCharacterMissingMessage)
                ->setInvalidValue($value)
                ->atPath($plainPasswordProperty)
                ->addViolation();
        }


        // Check whether there is at least one number present
        if (!\preg_match('/[0-9]/', $stringValue)) {
            $this->context
                ->buildViolation($constraint->numberMissingMessage)
                ->setInvalidValue($value)
                ->atPath($plainPasswordProperty)
                ->addViolation();
        }
    }
}
