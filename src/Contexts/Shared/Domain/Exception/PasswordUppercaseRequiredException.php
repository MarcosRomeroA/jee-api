<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Exception;

class PasswordUppercaseRequiredException extends \Exception
{
    public function __construct(string $message = "password_uppercase_required_exception")
    {
        parent::__construct($message);
    }
}