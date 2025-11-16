<?php

declare(strict_types=1);

namespace App\Apps\Web\Post\SearchPostComments;

use Symfony\Component\Validator\Constraints as Assert;

class SearchPostCommentsRequest
{
    #[Assert\NotNull, Assert\Type("string")]
    public string $id;

    #[Assert\Type("array")]
    public mixed $q = [];

    public ?int $limit = 10;
    public ?int $offset = 0;
}
