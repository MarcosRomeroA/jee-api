<?php declare(strict_types=1);

namespace App\Apps\Web\User\ResendEmailConfirmation;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\ResendEmailConfirmation\EmailConfirmationResender;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ResendEmailConfirmationController extends ApiController
{
    public function __construct(
        private readonly EmailConfirmationResender $emailConfirmationResender
    ) {
    }

    #[Route('/api/auth/resend-confirmation', name: 'resend_email_confirmation', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $userId = new Uuid($data['userId'] ?? '');

        $this->emailConfirmationResender->resend($userId);

        return new Response('', Response::HTTP_OK);
    }
}

