<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain;

interface PostRepository
{
    public function save(Post $post): void;
}