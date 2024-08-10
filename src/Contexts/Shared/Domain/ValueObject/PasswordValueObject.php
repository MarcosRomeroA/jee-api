<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\ValueObject;

use App\Contexts\Shared\Domain\Exception\PasswordMinimumLengthRequiredException;
use App\Contexts\Shared\Domain\Exception\PasswordMismatchException;
use App\Contexts\Shared\Domain\Exception\PasswordSpecialCharacterRequiredException;
use App\Contexts\Shared\Domain\Exception\PasswordUppercaseRequiredException;

abstract class PasswordValueObject
{
    public function __construct(protected string $value)
    {
        $this->hashPassword();
    }

    public function value(): string
    {
        return $this->value;
    }

    /**
     * @throws PasswordUppercaseRequiredException
     */
    protected function hasMinimumUppercase(string $value, int $minimumUppercase = 1): void{

        $validationResult = preg_match_all('/[A-Z]/', $value) >= $minimumUppercase;

        if (!$validationResult){
            throw new PasswordUppercaseRequiredException($minimumUppercase);
        }
    }

    /**
     * @throws PasswordSpecialCharacterRequiredException
     */
    protected function hasMinimumSpecialCharacters(string $value, int $minSpecialChars = 1): void
    {
        $validationResult = preg_match_all('/[^a-zA-Z0-9]/', $value) >= $minSpecialChars;

        if (!$validationResult){
            throw new PasswordSpecialCharacterRequiredException($minSpecialChars);
        }
    }

    protected function hasMinimumLength(string $value, int $minLength = 8): void
    {

        if (strlen($value) <= $minLength){
            throw new PasswordMinimumLengthRequiredException($minLength);
        }
    }

    /**
     * @throws PasswordMismatchException
     */
    public function matchWith($otherPassword): void{
        if ($this->value !== $otherPassword) {
            throw new PasswordMismatchException();
        }
    }

    public function hashPassword(): void
    {
        $this->value = password_hash($this->value, PASSWORD_BCRYPT);
    }

    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->value);
    }
}
