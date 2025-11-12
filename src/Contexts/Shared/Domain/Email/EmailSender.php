<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Email;

interface EmailSender
{
    public function send(Email $email): void;
}

