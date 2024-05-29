<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\ValueObject;

use App\Contexts\Shared\Domain\Exception\PasswordMismatchException;
use App\Contexts\Shared\Domain\ValueObject\PasswordValueObject;
use Doctrine\ORM\Mapping as ORM;

class PasswordValue extends PasswordValueObject
{
    #[ORM\Column(name:'password', type: 'string', length: 512)]
    protected string $value;


    /**
     * @throws PasswordMismatchException
     */
    public function __construct(string $password, string $confirmationPassword)
    {
        $this->validatePasswordsMatch($password, $confirmationPassword);
        parent::__construct($password);
    }
}