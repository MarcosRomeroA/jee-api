<?php declare(strict_types=1);

namespace App\Apps\Web\Notification\MarkAsRead;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;

final readonly class MarkAsReadRequest extends BaseRequest
{
    // No necesita propiedades adicionales ya que el ID viene por la URL
    // y el sessionId se obtiene automáticamente del JWT
}
