<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class UserCollectionResponse extends Response
{
    /**
     * @param array<UserResponse> $users
     */
    public function __construct(private readonly array $users)
    {
    }

    public function toArray(): array
    {
        $data = [];

        foreach($this->users as $user){
            $data[] = $user->toArray();
        }

        $response['data'] = $data;
        $response['metadata']['quantity'] = count($this->users);

        return $response;
    }
}