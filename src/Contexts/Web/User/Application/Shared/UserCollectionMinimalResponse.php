<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

class UserCollectionMinimalResponse extends Response
{
    /**
     * @param array<FollowResponse> $follows
     */
    public function __construct(private readonly array $follows)
    {
    }

    public function toArray(): array
    {
        $data = [];

        foreach($this->follows as $follow){
            $data[] = $follow->toArray();
        }

        $response['data'] = $data;
        $response['metadata']['quantity'] = count($this->follows);

        return $response;
    }
}