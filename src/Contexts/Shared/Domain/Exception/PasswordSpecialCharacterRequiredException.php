<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Exception;

class PasswordSpecialCharacterRequiredException extends \Exception
{
    public function __construct(string $message = "password_special_character_required_exception")
    {
        parent::__construct($message);
    }
}