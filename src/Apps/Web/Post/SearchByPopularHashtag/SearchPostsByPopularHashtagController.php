<?php

declare(strict_types=1);

namespace App\Apps\Web\Post\SearchByPopularHashtag;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\SearchByPopularHashtag\SearchPostsByPopularHashtagRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchPostsByPopularHashtagController extends ApiController
{
    public function __invoke(Request $request, string $hashtag): Response
    {
        $input = SearchPostsByPopularHashtagRequest::fromHttp($request, $hashtag);

        $this->validateRequest($input);

        $query = $input->toQuery();

        $response = $this->ask($query);

        return $this->collectionResponse($response);
    }
}
