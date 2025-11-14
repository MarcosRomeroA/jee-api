<?php declare(strict_types=1);

namespace App\Apps\Web\Auth\Refresh;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Shared\Infrastructure\Symfony\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetTokenByRefreshController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        // Validate request body before processing
        $data = json_decode($request->getContent(), true);
        if (
            !is_array($data) ||
            !isset($data["refreshToken"]) ||
            trim($data["refreshToken"]) === ""
        ) {
            throw new ValidationException([
                "refreshToken" => ["The refreshToken field is required"],
            ]);
        }

        $input = GetTokenByRefreshRequest::fromHttp($request);
        $this->validateRequest($input);

        $query = $input->toQuery();
        $response = $this->queryBus->ask($query);

        return $this->successResponse($response);
    }
}
