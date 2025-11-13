<?php declare(strict_types=1);

namespace App\Apps\Web\User\Create;

use App\Contexts\Web\User\Application\Create\CreateUserCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateUserRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $id,

        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $firstname,

        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $lastname,

        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $username,

        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,

        #[Assert\NotBlank]
        #[Assert\Type("string")]
        #[Assert\Length(min: 6)]
        public ?string $password = null,

        #[Assert\Type("string")]
        public ?string $confirmationPassword = null,
    ) {}

    public static function fromHttp(Request $request, string $id): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            $id,
            $data['firstname'] ?? '',
            $data['lastname'] ?? '',
            $data['username'] ?? '',
            $data['email'] ?? '',
            $data['password'] ?? null,
            $data['confirmationPassword'] ?? null
        );
    }

    public function toCommand(): CreateUserCommand
    {
        return new CreateUserCommand(
            $this->id,
            $this->firstname,
            $this->lastname,
            $this->username,
            $this->email,
            $this->password,
            $this->confirmationPassword
        );
    }
}
