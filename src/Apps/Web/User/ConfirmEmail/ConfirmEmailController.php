<?php declare(strict_types=1);

namespace App\Apps\Web\User\ConfirmEmail;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\ConfirmEmail\EmailConfirmer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ConfirmEmailController extends ApiController
{
    public function __construct(
        private readonly EmailConfirmer $emailConfirmer
    ) {
    }

    #[Route('/api/auth/confirm-email/{token}', name: 'confirm_email', methods: ['GET'])]
    public function __invoke(string $token): Response
    {
        $this->emailConfirmer->confirm($token);

        return new Response('', Response::HTTP_OK);
    }
}

