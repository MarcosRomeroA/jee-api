<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class UserAlreadyMemberException extends ApiException
{
    public function __construct(string $message = "User is already a member of this team")
    {
        parent::__construct(
            $message,
            'user_already_member_exception',
            Response::HTTP_CONFLICT
        );
    }
}
