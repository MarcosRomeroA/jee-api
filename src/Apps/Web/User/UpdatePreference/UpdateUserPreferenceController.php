<?php declare(strict_types=1);

namespace App\Apps\Web\User\UpdatePreference;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UpdateUserPreferenceController extends ApiController
{
    public function __invoke(Request $request, string $sessionId): Response
    {
        $input = UpdateUserPreferenceRequest::fromHttp($request, $sessionId);
        $this->validateRequest($input);

        $command = $input->toCommand();
        $this->dispatch($command);

        return $this->successEmptyResponse();
    }
}
