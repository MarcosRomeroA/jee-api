<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Symfony;

use App\Contexts\Shared\Domain\CQRS\Command\Command;
use App\Contexts\Shared\Domain\CQRS\Command\CommandBus;
use App\Contexts\Shared\Domain\CQRS\Query\Query;
use App\Contexts\Shared\Domain\CQRS\Query\QueryBus;
use App\Contexts\Shared\Domain\CQRS\Query\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use function Lambdish\Phunctional\each;

abstract class ApiController extends AbstractController
{
    public function __construct(
        protected QueryBus                 $queryBus,
        protected CommandBus               $commandBus,
    )
    {
    }

    protected function ask(Query $query): ?Response
    {
        return $this->queryBus->ask($query);
    }

    protected function dispatch(Command $command): void
    {
        $this->commandBus->dispatch($command);
    }

    public function successResponse(mixed $message, int $code = SymfonyResponse::HTTP_OK) : JsonResponse
    {
        if(is_object($message))
            $message = $message->toArray();

        return new JsonResponse(['data'=> $message], $code);
    }

    public function successEmptyResponse(int $code = SymfonyResponse::HTTP_OK) : SymfonyResponse
    {
        return new SymfonyResponse('', $code);
    }

    public function successCreatedResponse() : SymfonyResponse
    {
        return new SymfonyResponse('', SymfonyResponse::HTTP_CREATED);
    }

    public function collectionResponse(mixed $message, $code = 200) : JsonResponse
    {
        return new JsonResponse($message->toArray(), $code);
    }
}