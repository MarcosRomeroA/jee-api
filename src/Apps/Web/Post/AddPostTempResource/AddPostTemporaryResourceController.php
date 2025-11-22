<?php declare(strict_types=1);

namespace App\Apps\Web\Post\AddPostTempResource;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddPostTemporaryResourceController extends ApiController
{
    public function __invoke(Request $request, string $id, string $sessionId): Response
    {
        $input = AddPostTemporaryResourceRequest::fromHttp($request);
        $this->validateRequest($input);

        $command = $input->toCommand($id, $this->getParameter('kernel.project_dir'));

        $this->dispatch($command);

        return $this->successEmptyResponse(Response::HTTP_ACCEPTED);
    }
}
