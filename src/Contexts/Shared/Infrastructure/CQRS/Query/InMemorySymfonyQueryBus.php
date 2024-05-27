<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\CQRS\Query;

use App\Contexts\Shared\Domain\CQRS\Query\Query;
use App\Contexts\Shared\Domain\CQRS\Query\QueryBus;
use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Shared\Infrastructure\CQRS\CallableFirstParameterExtractor;
use App\Shared\Infrastructure\CQRS\Query\HandlerFailedException;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class InMemorySymfonyQueryBus implements QueryBus
{
    private MessageBus $bus;

    public function __construct(iterable $queryHandlers)
    {
        $this->bus = new MessageBus(
            [
                new HandleMessageMiddleware(
                    new HandlersLocator(CallableFirstParameterExtractor::forCallables($queryHandlers))
                ),
            ]
        );
    }

    public function ask(Query $query): ?Response
    {
        try {
            $stamp = $this->bus->dispatch($query)->last(HandledStamp::class);

            return $stamp->getResult();
        } catch (NoHandlerForMessageException) {
            throw new QueryNotRegisteredError($query);
        }
        catch(HandlerFailedException $e)
        {
            $previusExeption = $e->getPrevious();
            $class = get_class($previusExeption);
            throw new $class($previusExeption->getMessage());
        }
    }
}
