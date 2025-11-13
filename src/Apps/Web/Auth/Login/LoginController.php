<?php declare(strict_types=1);

namespace App\Apps\Web\Auth\Login;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        $input = LoginRequest::fromHttp($request);
        $this->validateRequest($input);

        $query = $input->toQuery();
        $response = $this->queryBus->ask($query);

        return $this->successResponse($response);
    }
}