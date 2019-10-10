# PasswordValidator
Symfony validation for Intracto's standard password policy

### The policy consists of:
- minimum length (defaults on 8 characters)
- at least one upper cased character
- at least one lower cased character
- at least one number
- cannot equal the username
- maximum length (defaults on integer limit) - de facto optional

## installation

Using composer:

```
composer require intracto/password-validator
```

The password constraint and -validator are dependent on both `symfony/validator` and `symfony/security-core`.


## Usage

The validator is designed as a class validator on an entity implementing the `Symfony\Component\Security\Core\User\UserInterface`.

In order to validate the password, you need to provide the accessor and property path to the plain password which needs validation.

A basic working example would be:


```

/**
 * @Password(plainPasswordAccessor="getPlainPassword", plainPasswordProperty="plain_password")
 */
class User implements UserInterface
{

   /** @var string */
   private $plain_password;
    
   /** @return string */
   public function getPlainPassword(): string
   {
       return $this->plain_password;
   }
      
      ...

}
```

## Contributions

Do you feel the code or policy are too rigid, or just not strict enough? Feel free to open up an issue and/or submit a pull request with your suggestions.
