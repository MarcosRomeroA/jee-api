<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\ValueObject;

use App\Contexts\Shared\Domain\Exception\PasswordMismatchException;
use App\Contexts\Shared\Domain\Exception\PasswordSpecialCharacterRequiredException;
use App\Contexts\Shared\Domain\Exception\PasswordUppercaseRequiredException;

abstract class PasswordValueObject extends StringValueObject
{
    public function __construct(protected string $value)
    {
        parent::__construct($value);
    }

    /**
     * @throws PasswordUppercaseRequiredException
     */
    protected function hasMinimumUppercase(string $value, int $minimumUppercase = 0): void{

        $validationResult = preg_match_all('/[A-Z]/', $value) >= $minimumUppercase;

        if (!$validationResult){
            throw new PasswordUppercaseRequiredException();
        }
    }

    /**
     * @throws PasswordSpecialCharacterRequiredException
     */
    protected function hasMinimumSpecialCharacters(string $value, int $minSpecialChars = 1): void
    {
        $validationResult = preg_match_all('/[^a-zA-Z0-9]/', $value) >= $minSpecialChars;

        if (!$validationResult){
            throw new PasswordSpecialCharacterRequiredException();
        }
    }

    /**
     * @throws PasswordMismatchException
     */
    protected function validatePasswordsMatch($password, $confirmationPassword): void{
        if ($password !== $confirmationPassword) {
            throw new PasswordMismatchException();
        }
    }
}
