<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;

final class NotificationNotFoundException extends ApiException
{
    public function __construct()
    {
        parent::__construct('Notification not found');
    }
}
