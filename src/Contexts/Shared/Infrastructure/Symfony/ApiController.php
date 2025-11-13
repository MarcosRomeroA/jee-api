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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Contexts\Shared\Infrastructure\Symfony\Exception\ValidationException;

abstract class ApiController extends AbstractController
{
    public function __construct(
        protected QueryBus                 $queryBus,
        protected CommandBus               $commandBus,
        protected ValidatorInterface        $validator,
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

    protected function validateRequest(object $dto): void
    {
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            $errData = [];
            foreach ($errors as $error) {
                $errData[$error->getPropertyPath()][] = $error->getMessage();
            }

            throw new ValidationException($errData);
        }
    }
}