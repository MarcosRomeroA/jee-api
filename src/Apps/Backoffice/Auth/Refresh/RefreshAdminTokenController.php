<?php declare(strict_types=1);

namespace App\Apps\Backoffice\Auth\Refresh;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Shared\Infrastructure\Symfony\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RefreshAdminTokenController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        if (
            !is_array($data) ||
            !isset($data['refreshToken']) ||
            trim($data['refreshToken']) === ''
        ) {
            throw new ValidationException([
                'refreshToken' => ['The refreshToken field is required'],
            ]);
        }

        $input = RefreshAdminTokenRequest::fromHttp($request);
        $this->validateRequest($input);

        $query = $input->toQuery();
        $response = $this->ask($query);

        return $this->successResponse($response);
    }
}
