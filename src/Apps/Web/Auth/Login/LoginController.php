<?php declare(strict_types=1);

namespace App\Apps\Web\Auth\Login;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Auth\Application\Login\LoginUserQuery;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends ApiController
{
    public function __invoke(LoginRequest $request): Response
    {
        $query = new LoginUserQuery(
            $request->email,
            $request->password
        );

        $response = $this->queryBus->ask($query);

        return $this->successResponse($response);
    }
}