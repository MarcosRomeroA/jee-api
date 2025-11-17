# Guía Completa: Bounded Context de Post

Este documento proporciona una visión completa del bounded context de Post en la arquitectura DDD/CQRS del proyecto. Incluye patrones, ejemplos de código y cómo seguir la misma estructura para otros bounded contexts.

## Tabla de Contenidos

1. [Arquitectura General](#arquitectura-general)
2. [Autenticación (JWT Middleware)](#autenticación-jwt-middleware)
3. [Controllers](#controllers)
4. [Requests/DTOs](#requestsdtos)
5. [Commands](#commands)
6. [Queries](#queries)
7. [Responses](#responses)
8. [Configuración de Rutas](#configuración-de-rutas)
9. [Flujo Completo: Ejemplo Práctico](#flujo-completo-ejemplo-práctico)

---

## Arquitectura General

```
┌─────────────────────────────────────────────────────────┐
│                    HTTP Request (API)                    │
└────────────────────────┬────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────┐
│            JwtAuthMiddleware (onKernelRequest)           │
│   - Valida token JWT                                    │
│   - Extrae sessionId del token                          │
│   - Lo agrega como atributo en la request               │
└────────────────────────┬────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────┐
│              Controller (Apps/Web/Post/...)              │
│   - Recibe Request con sessionId                         │
│   - Crea Request DTO (validación básica)                │
│   - Convierte DTO a Command/Query                        │
│   - Dispone Command o ejecuta Query                      │
└────────────────────────┬────────────────────────────────┘
                         │
        ┌────────────────┴────────────────┐
        │                                 │
        ▼ Command                         ▼ Query
┌──────────────────────┐         ┌──────────────────────┐
│ CommandHandler       │         │ QueryHandler         │
│ - Valida datos       │         │ - Busca datos        │
│ - Ejecuta lógica     │         │ - Retorna Response   │
│ - Persiste cambios   │         │ - Sin efectos lado   │
└──────────────────────┘         └──────────────────────┘
        │                                 │
        ▼                                 ▼
   Domain Layer                     Response DTO
     (Entities,                      (toArray())
      Value Objects,                     │
      Repositories)                      │
                         │
                         ▼
           JsonResponse (API Response)
```

---

## Autenticación (JWT Middleware)

### Ubicación
`src/Contexts/Shared/Infrastructure/Symfony/JwtAuthMiddleware.php`

### Código Completo

```php
<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Symfony;

use App\Contexts\Shared\Domain\Exception\ExpiredTokenException;
use App\Contexts\Shared\Domain\Exception\UnauthorizedException;
use App\Contexts\Shared\Domain\Jwt\JwtGenerator;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final readonly class JwtAuthMiddleware
{
    public function __construct(
        private JwtGenerator $jwtGenerator,
    )
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // Verificar si la ruta requiere autenticación (atributo "auth" en la configuración)
        $shouldAuthenticate = $event->getRequest()->attributes->get('auth', false);

        // Extraer token del header Authorization
        $jwtToken = $event->getRequest()->headers->get('Authorization');

        if (!$shouldAuthenticate)
            return;

        if (!$jwtToken)
            throw new UnauthorizedException();

        // Verificar y decodificar el token
        $payload = $this->verify($jwtToken);

        // Guardar el ID del usuario en los atributos de la request
        $request->attributes->set('sessionId', $payload['id']);
    }

    public function verify(string $bearer): array
    {
        try {
            $this->jwtGenerator->verify($bearer);
        }catch (\Exception $e){
            throw new ExpiredTokenException();
        }

        return $this->jwtGenerator->decode($bearer);
    }
}
```

### Configuración en services.yaml

```yaml
App\Contexts\Shared\Infrastructure\Symfony\JwtAuthMiddleware:
    tags:
        - {
              name: kernel.event_listener,
              event: kernel.request,
              method: onKernelRequest,
          }
```

### Cómo se Activa

En la configuración de rutas, se utiliza el atributo `defaults: { auth: true }`:

```yaml
create_post:
    path: /post/{id}
    controller: App\Apps\Web\Post\Create\CreatePostController
    methods: [PUT]
    defaults: { auth: true }  # Activa la validación del JWT
```

### Flujo de Autenticación

1. Cliente envía: `Authorization: <token_jwt>`
2. Middleware intercepta la request
3. Valida que `auth: true` en la ruta
4. Verifica el JWT usando `JwtGenerator`
5. Decodifica el payload para obtener el `id` (userId)
6. Agrega `sessionId` a los atributos de la request
7. El controller recibe `sessionId` como parámetro

---

## Controllers

Los controllers en este proyecto siguen un patrón DDD/CQRS donde son **muy finos**:
- No contienen lógica de negocio
- Reciben DTOs/Requests
- Validan la request
- Crean Command/Query
- Disponen el Command o preguntan la Query

### Clase Base: ApiController

**Ubicación:** `src/Contexts/Shared/Infrastructure/Symfony/ApiController.php`

```php
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
        protected QueryBus $queryBus,
        protected CommandBus $commandBus,
        protected ValidatorInterface $validator,
    ) {}

    protected function ask(Query $query): ?Response
    {
        return $this->queryBus->ask($query);
    }

    protected function dispatch(Command $command): void
    {
        $this->commandBus->dispatch($command);
    }

    public function successResponse(
        mixed $message,
        int $code = SymfonyResponse::HTTP_OK,
    ): JsonResponse {
        if (is_object($message)) {
            $message = $message->toArray();
        }

        return new JsonResponse(["data" => $message], $code);
    }

    public function successEmptyResponse(
        int $code = SymfonyResponse::HTTP_OK,
    ): SymfonyResponse {
        return new SymfonyResponse("", $code);
    }

    public function successCreatedResponse(): SymfonyResponse
    {
        return new SymfonyResponse("", SymfonyResponse::HTTP_CREATED);
    }

    public function collectionResponse(
        mixed $message,
        $code = 200,
    ): JsonResponse {
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
```

### Ejemplo 1: Controller para Command (CREATE)

**Ubicación:** `src/Apps/Web/Post/Create/CreatePostController.php`

```php
<?php declare(strict_types=1);

namespace App\Apps\Web\Post\Create;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\Create\CreatePostCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreatePostController extends ApiController
{
    public function __invoke(Request $request, string $id, string $sessionId): Response
    {
        // 1. Crear DTO desde la request HTTP
        $input = CreatePostRequest::fromHttp($request, $id, $sessionId);
        
        // 2. Validar el DTO
        $this->validateRequest($input);

        // 3. Convertir DTO a Command
        $command = $input->toCommand();
        
        // 4. Disponer el Command
        $this->commandBus->dispatch($command);

        // 5. Retornar respuesta vacía con código 200
        return $this->successEmptyResponse();
    }
}
```

**Parámetros:**
- `Request $request`: Objeto de Symfony con los datos HTTP
- `string $id`: Parámetro de la URL (`{id}`)
- `string $sessionId`: Inyectado por el middleware JWT

### Ejemplo 2: Controller para Query (READ)

**Ubicación:** `src/Apps/Web/Post/Find/FindPostController.php`

```php
<?php declare(strict_types=1);

namespace App\Apps\Web\Post\Find;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\Find\FindPostQuery;
use Symfony\Component\HttpFoundation\Response;

final class FindPostController extends ApiController
{
    public function __invoke(string $id): Response
    {
        // 1. Crear la Query
        $query = new FindPostQuery($id);

        // 2. Ejecutar la Query
        $response = $this->queryBus->ask($query);

        // 3. Retornar respuesta con los datos
        return $this->successResponse($response);
    }
}
```

### Ejemplo 3: Controller para Action (Like/Dislike)

**Ubicación:** `src/Apps/Web/Post/Like/LikePostController.php`

```php
<?php declare(strict_types=1);

namespace App\Apps\Web\Post\Like;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\Like\LikePostCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LikePostController extends ApiController
{
    public function __invoke(Request $request, string $id, string $sessionId): Response
    {
        // Crear Command directamente sin DTO (datos simples)
        $command = new LikePostCommand(
            $id,
            $sessionId  // userId del usuario autenticado
        );

        // Disponer el Command
        $this->commandBus->dispatch($command);

        // Retornar respuesta vacía
        return $this->successEmptyResponse();
    }
}
```

### Ejemplo 4: Controller con Búsqueda (Query con Criterios)

**Ubicación:** `src/Apps/Web/Post/Search/SearchPostsController.php`

```php
<?php declare(strict_types=1);

namespace App\Apps\Web\Post\Search;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchPostsController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        // 1. Crear DTO desde la request
        $input = SearchPostsRequest::fromHttp($request);
        
        // 2. Validar el DTO
        $this->validateRequest($input);

        // 3. Convertir DTO a Query
        $query = $input->toQuery();
        
        // 4. Ejecutar la Query
        $response = $this->queryBus->ask($query);

        // 5. Retornar respuesta de colección
        return $this->collectionResponse($response);
    }
}
```

### Ejemplo 5: Controller con Solicitud Decorada

**Ubicación:** `src/Apps/Web/Post/AddPostComment/AddPostCommentController.php`

```php
<?php declare(strict_types=1);

namespace App\Apps\Web\Post\AddPostComment;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\AddComment\AddCommentPostCommand;
use Symfony\Component\HttpFoundation\Response;

class AddPostCommentController extends ApiController
{
    public function __invoke(
        AddPostCommentRequest $request,  // Inyección de dependencia del DTO
        string $id,
        string $sessionId
    ): Response
    {
        // El DTO se inyecta automáticamente
        // Symfony valida el DTO antes de llamar al controller
        
        // Crear Command con datos del DTO
        $command = new AddCommentPostCommand(
            $id,
            $sessionId,
            $request->commentId,
            $request->commentBody,
        );

        // Disponer el Command
        $this->commandBus->dispatch($command);

        // Retornar respuesta vacía
        return $this->successEmptyResponse();
    }
}
```

---

## Requests/DTOs

Los Requests/DTOs son **Data Transfer Objects** que:
- Mapean datos HTTP a objetos PHP
- Validan usando atributos de Symfony Validator
- Convierten a Command/Query
- NO contienen lógica de negocio

### Clase Base: BaseRequest

**Ubicación:** `src/Contexts/Shared/Infrastructure/Symfony/BaseRequest.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Symfony;

use App\Contexts\Shared\Domain\Exception\ValidationException;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract readonly class BaseRequest
{
    /**
     * @throws ReflectionException
     */
    public function __construct(
        protected ValidatorInterface $validator,
        protected RequestStack $requestStack
    )
    {
        $this->populate();      // Llenar propiedades desde HTTP
        $this->validate();      // Validar propiedades
    }

    public function validate(): void
    {
        $errors = $this->validator->validate($this);

        $errorMessages = [];

        foreach ($errors as $message) {
            $errorMessages[] = [$message->getPropertyPath() => $message->getMessage()];
        }

        if (count($errorMessages) > 0) {
            throw new ValidationException($errorMessages);
        }
    }

    public function getRequest(): array
    {
        $request = $this->requestStack->getCurrentRequest() ?? Request::createFromGlobals();
        $q = $this->getCriteria($request->query->get('q'));

        try{
            $requestData = $request->toArray();
            if ($q){
                $q['limit'] = isset($q['limit']) ? (int)$q['limit'] : 10;
                $q['offset'] = isset($q['offset']) ? (int)$q['offset'] : 0;
                $requestData['q'] = $q;
            }
            return $requestData;
        }
        catch (\Exception){
            $requestData = [];
            if ($q){
                $q['limit'] = isset($q['limit']) ? (int)$q['limit'] : 10;
                $q['offset'] = isset($q['offset']) ? (int)$q['offset'] : 0;
                $requestData['q'] = $q;
            }
            return $requestData;
        }
    }

    /**
     * @throws ReflectionException
     */
    protected function populate(): void
    {
        $requestData = $this->getRequest();
        $reflectionClass = new ReflectionClass($this);

        foreach ($requestData as $property => $value) {
            $reflectionProperty = $reflectionClass->getProperty($property);

            if ($reflectionProperty->isPublic() && !$reflectionProperty->isStatic()) {
                $reflectionProperty->setValue($this, $value);
            }
        }
    }

    private function getCriteria(?string $q): ?array{
        if (!is_null($q)){
            $parameters = explode(';', $q);
            $criteria = [];
            foreach ($parameters as $parameter){
                $key = explode(':', $parameter)[0];
                $value = explode(':', $parameter)[1];
                $criteria[$key] = $value;
            }
            return $criteria;
        }
        else{
            return null;
        }
    }
}
```

### Ejemplo 1: Request Simple (FromHttp)

**Ubicación:** `src/Apps/Web/Post/Create/CreatePostRequest.php`

```php
<?php declare(strict_types=1);

namespace App\Apps\Web\Post\Create;

use App\Contexts\Web\Post\Application\Create\CreatePostCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreatePostRequest
{
    public function __construct(
        public string $id,
        public string $sessionId,

        #[Assert\NotBlank]  // No puede estar vacío
        #[Assert\Type("string")]
        public string $body,

        #[Assert\Type("array")]
        public array $resources = [],

        #[Assert\Type("string")]
        public ?string $sharedPostId = null,
    ) {}

    // Método estático para crear desde HTTP
    public static function fromHttp(Request $request, string $id, string $sessionId): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            $id,
            $sessionId,
            $data['body'] ?? '',
            $data['resources'] ?? [],
            $data['sharedPostId'] ?? null
        );
    }

    // Método para convertir a Command
    public function toCommand(): CreatePostCommand
    {
        return new CreatePostCommand(
            $this->id,
            $this->body,
            $this->resources,
            $this->sharedPostId,
            $this->sessionId
        );
    }
}
```

**Request HTTP esperada:**
```json
PUT /api/post/{id}
Authorization: <jwt_token>
Content-Type: application/json

{
    "body": "Mi nuevo post",
    "resources": ["image1.jpg", "image2.jpg"],
    "sharedPostId": "uuid-optional"
}
```

### Ejemplo 2: Request con Herencia de BaseRequest

**Ubicación:** `src/Apps/Web/Post/AddPostComment/AddPostCommentRequest.php`

```php
<?php declare(strict_types=1);

namespace App\Apps\Web\Post\AddPostComment;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class AddPostCommentRequest extends BaseRequest
{
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $commentId;
    
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $commentBody;
}
```

**Uso en Controller:**
```php
// Symfony inyecta y valida automáticamente
public function __invoke(
    AddPostCommentRequest $request,
    string $id,
    string $sessionId
): Response
```

### Ejemplo 3: Request para Búsqueda

**Ubicación:** `src/Apps/Web/Post/Search/SearchPostsRequest.php`

```php
<?php declare(strict_types=1);

namespace App\Apps\Web\Post\Search;

use App\Contexts\Web\Post\Application\Search\SearchPostQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchPostsRequest
{
    public function __construct(
        #[Assert\Type("array")] 
        public ?array $q = null,  // Criterios de búsqueda
    ) {}

    // Extraer criterios de la query string
    public static function fromHttp(Request $request): self
    {
        $q = $request->query->get("q");
        return new self($q ? ["q" => $q] : null);
    }

    // Convertir a Query
    public function toQuery(): SearchPostQuery
    {
        return new SearchPostQuery($this->q);
    }
}
```

**Request HTTP esperada:**
```
GET /api/posts?q=limit:10;offset:0
Authorization: <jwt_token>
```

### Ejemplo 4: Request para Feed Personal

**Ubicación:** `src/Apps/Web/Post/SearchMyFeed/SearchMyFeedRequest.php`

```php
<?php declare(strict_types=1);

namespace App\Apps\Web\Post\SearchMyFeed;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchMyFeedRequest extends BaseRequest
{
    #[Assert\Type("array")]
    public mixed $q;  // Criterios de búsqueda (heredado de BaseRequest)
}
```

---

## Commands

Los Commands representan **acciones que modifican el estado**. Están en la capa de Application y:
- Son muy simples (solo datos)
- Implementan la interfaz `Command`
- Son immutables (readonly)
- Se convierten desde DTOs

### Interfaz: Command

```php
<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\CQRS\Command;

interface Command {}
```

### Ejemplo 1: CreatePostCommand

**Ubicación:** `src/Contexts/Web/Post/Application/Create/CreatePostCommand.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class CreatePostCommand implements Command
{
    public function __construct(
        public string $id,
        public string $body,
        public array $resources,
        public ?string $sharedPostId,
        public string $userId,  // Del sessionId del middleware
    )
    {
    }
}
```

### Ejemplo 2: LikePostCommand

**Ubicación:** `src/Contexts/Web/Post/Application/Like/LikePostCommand.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Like;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class LikePostCommand implements Command
{
    public function __construct(
        public string $postId,
        public string $userId  // Del sessionId del middleware
    )
    {
    }
}
```

### Ejemplo 3: DeletePostCommand

**Ubicación:** `src/Contexts/Web/Post/Application/Delete/DeletePostCommand.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Delete;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class DeletePostCommand implements Command
{
    public function __construct(
        public string $postId,
        public string $userId  // Para verificar que el usuario es el propietario
    )
    {
    }
}
```

### Ejemplo 4: AddCommentPostCommand

**Ubicación:** `src/Contexts/Web/Post/Application/AddComment/AddCommentPostCommand.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\AddComment;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class AddCommentPostCommand implements Command
{
    public function __construct(
        public string $postId,
        public string $userId,
        public string $commentId,
        public string $commentBody
    )
    {
    }
}
```

---

## Queries

Las Queries representan **lecturas sin efectos secundarios**. Están en la capa de Application y:
- Son muy simples (solo datos)
- Implementan la interfaz `Query`
- Son immutables (readonly)
- Retornan un Response

### Interfaz: Query

```php
<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\CQRS\Query;

interface Query {}
```

### Ejemplo 1: FindPostQuery

**Ubicación:** `src/Contexts/Web/Post/Application/Find/FindPostQuery.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Find;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class FindPostQuery implements Query
{
    public function __construct(
        public string $id,  // ID del post a obtener
    )
    {
    }
}
```

### Ejemplo 2: SearchPostQuery

**Ubicación:** `src/Contexts/Web/Post/Application/Search/SearchPostQuery.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchPostQuery implements Query
{
    public function __construct(
        public ?array $criteria = null  // Criterios: limit, offset, etc.
    )
    {
    }
}
```

### Ejemplo 3: SearchMyFeedQuery

**Ubicación:** `src/Contexts/Web/Post/Application/SearchMyFeed/SearchMyFeedQuery.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchMyFeed;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final class SearchMyFeedQuery implements Query
{
    public function __construct(
        public string $id,                  // ID del usuario (sessionId)
        public ?array $criteria = null,     // Criterios de búsqueda
    )
    {
    }
}
```

### Ejemplo 4: SearchPostLikesQuery

**Ubicación:** `src/Contexts/Web/Post/Application/SearchPostLikes/SearchPostLikesQuery.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchPostLikes;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final class SearchPostLikesQuery implements Query
{
    public function __construct(
        public string $postId,
        public ?array $criteria = null
    )
    {
    }
}
```

---

## Handlers

Los Handlers ejecutan la lógica de negocio. Hay dos tipos: CommandHandler y QueryHandler.

### Command Handler

**Interfaz:**
```php
<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\CQRS\Command;

interface CommandHandler {}
```

**Ejemplo: CreatePostCommandHandler**

**Ubicación:** `src/Contexts/Web/Post/Application/Create/CreatePostCommandHandler.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\ValueObject\BodyValue;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class CreatePostCommandHandler implements CommandHandler
{
    public function __construct(
        private PostCreator $creator,
        private UserRepository $userRepository,
    )
    {
    }

    public function __invoke(CreatePostCommand $command): void
    {
        // 1. Convertir datos primitivos a Value Objects
        $id = new Uuid($command->id);
        $body = new BodyValue($command->body);
        
        // 2. Obtener agregados del repositorio
        $user = $this->userRepository->findById(new Uuid($command->userId));

        // 3. Convertir ID compartido si existe
        $sharedPostId = null;
        if ($command->sharedPostId){
            $sharedPostId = new Uuid($command->sharedPostId);
        }

        // 4. Ejecutar la lógica de negocio (en la clase PostCreator)
        $this->creator->__invoke($id, $body, $user, $command->resources, $sharedPostId);
    }
}
```

**Ejemplo: LikePostCommandHandler**

**Ubicación:** `src/Contexts/Web/Post/Application/Like/LikePostCommandHandler.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Like;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class LikePostCommandHandler implements CommandHandler
{
    public function __construct(
        private PostLiker $liker,
    )
    {
    }

    public function __invoke(LikePostCommand $command): void
    {
        // Convertir a Value Objects y ejecutar
        $postId = new Uuid($command->postId);
        $userId = new Uuid($command->userId);
        $this->liker->__invoke($postId, $userId);
    }
}
```

### Query Handler

**Interfaz:**
```php
<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\CQRS\Query;

interface QueryHandler {}

interface Response {}
```

**Ejemplo: FindPostQueryHandler**

**Ubicación:** `src/Contexts/Web/Post/Application/Find/FindPostQueryHandler.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Find;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Application\Shared\PostResponse;
use Exception;

final readonly class FindPostQueryHandler implements QueryHandler
{
    public function __construct(
        private PostFinder $finder
    )
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke(FindPostQuery $query): PostResponse
    {
        // Convertir ID a Value Object
        $id = new Uuid($query->id);

        // Ejecutar la búsqueda
        return $this->finder->__invoke($id);
    }
}
```

**Ejemplo: SearchPostQueryHandler**

**Ubicación:** `src/Contexts/Web/Post/Application/Search/SearchPostQueryHandler.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Web\Post\Application\Shared\PostCollectionResponse;
use Exception;

final readonly class SearchPostQueryHandler implements QueryHandler
{
    public function __construct(
        private PostSearcher $searcher
    )
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke(SearchPostQuery $query): PostCollectionResponse
    {
        // Delegar a PostSearcher
        return $this->searcher->__invoke($query->criteria);
    }
}
```

---

## Responses

Los Responses son DTOs que se retornan desde los QueryHandlers. 

### Clase Base: Response

```php
<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\CQRS\Query;

abstract class Response {}
```

### Ejemplo 1: PostResponse (Entidad Individual)

**Ubicación:** `src/Contexts/Web/Post/Application/Shared/PostResponse.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Post\Domain\Post;

final class PostResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $body,
        public readonly string $username,
        public readonly array $resources,
        public readonly string $createdAt,
        public readonly ?string $urlProfileImage,
        public readonly ?array $sharedPost,
        public readonly ?int $likesQuantity,
        public readonly ?int $sharesQuantity,
        public readonly ?int $commentsQuantity
    )
    {
    }

    // Factory method desde entidad de dominio
    public static function fromEntity(Post $post, bool $hasShared = false): self
    {
        $sharedPostResponse = null;
        if ($post->getSharedPost() && $hasShared){
            $sharedPostResponse = self::fromEntity($post->getSharedPost());
        }

        return new self(
            $post->getId()->value(),
            $post->getBody()->value(),
            $post->getUser()->getUsername()->value(),
            $post->getResourceUrls(),
            $post->getCreatedAt()->value()->format('Y-m-d H:i:s'),
            $post->getUser()->getUrlProfileImage(),
            $sharedPostResponse?->toArray(),
            count($post->getLikes()->toArray()),
            $post->getSharesQuantity(),
            count($post->getComments()->toArray())
        );
    }

    // Convertir a array para la respuesta HTTP
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
```

**Respuesta HTTP:**
```json
{
    "data": {
        "id": "550e8400-e29b-41d4-a716-446655440000",
        "body": "Mi primer post",
        "username": "juan",
        "resources": ["https://...image1.jpg"],
        "createdAt": "2024-01-15 14:30:00",
        "urlProfileImage": "https://...profile.jpg",
        "sharedPost": null,
        "likesQuantity": 5,
        "sharesQuantity": 2,
        "commentsQuantity": 3
    }
}
```

### Ejemplo 2: PostCollectionResponse (Colección)

**Ubicación:** `src/Contexts/Web/Post/Application/Shared/PostCollectionResponse.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Post\Domain\Post;

class PostCollectionResponse extends Response
{
    /** @var Post[] */
    public array $posts;
    public int $limit;
    public int $offset;
    public int $total;

    /**
     * @param array<Post> $posts
     * @param array{limit: int, offset: int} $criteria
     * @param int $total
     */
    public function __construct(array $posts, array $criteria, int $total = 0)
    {
        $this->posts = $posts;
        $this->limit = $criteria["limit"];
        $this->offset = $criteria["offset"];
        $this->total = $total;
    }

    // Convertir a array con metadata de paginación
    public function toArray(): array
    {
        $response = [];

        foreach($this->posts as $post){
            $response[] = PostResponse::fromEntity($post, true)->toArray();
        }

        return [
            'data' => $response,
            'metadata' => [
                'limit' => $this->limit,
                'offset' => $this->offset,
                'total' => $this->total,
                'count' => count($this->posts)
            ]
        ];
    }
}
```

**Respuesta HTTP:**
```json
{
    "data": [
        {
            "id": "550e8400-e29b-41d4-a716-446655440000",
            "body": "Mi primer post",
            ...
        }
    ],
    "metadata": {
        "limit": 10,
        "offset": 0,
        "total": 45,
        "count": 10
    }
}
```

### Ejemplo 3: PostCommentResponse

**Ubicación:** `src/Contexts/Web/Post/Application/Shared/PostCommentResponse.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Post\Domain\Comment;

class PostCommentResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $comment,
        public readonly string $user,
        public readonly string $createdAt
    )
    {
    }

    public static function fromEntity(Comment $comment): self
    {
        return new self(
            $comment->getId()->value(),
            $comment->getComment()->value(),
            $comment->getUser()->getUsername()->value(),
            $comment->getCreatedAt()->value()->format('Y-m-d H:i:s')
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
```

### Ejemplo 4: LikeResponse

**Ubicación:** `src/Contexts/Web/Post/Application/Shared/LikeResponse.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class LikeResponse extends Response
{
    public function __construct(
        public readonly string $userId,
        public readonly string $username,
        public readonly string $firstname,
        public readonly string $lastname,
        public readonly ?string $profileImage,
        public readonly string $likedAt,
    ) {
    }

    public function toArray(): array
    {
        return [
            'userId' => $this->userId,
            'username' => $this->username,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'profileImage' => $this->profileImage,
            'likedAt' => $this->likedAt,
        ];
    }
}
```

---

## Configuración de Rutas

Las rutas definen:
- El path HTTP
- El controller a ejecutar
- Los métodos HTTP permitidos
- Si requiere autenticación (`auth: true`)

### Archivo Principal: config/routes/web/post.yaml

```yaml
# Crear un nuevo post
create_post:
    path: /post/{id}
    controller: App\Apps\Web\Post\Create\CreatePostController
    methods: [PUT]
    defaults: { auth: true }

# Obtener un post específico
find_post:
    path: /post/{id}
    controller: App\Apps\Web\Post\Find\FindPostController
    methods: [GET]
    defaults: { auth: true }

# Obtener múltiples posts en batch
find_posts_batch:
    path: /posts/batch
    controller: App\Apps\Web\Post\FindBatch\FindPostsByIdsController
    methods: [GET]
    defaults: { auth: true }

# Buscar posts (con criterios)
search_posts:
    path: /posts
    controller: App\Apps\Web\Post\Search\SearchPostsController
    methods: [GET]
    defaults: { auth: true }

# Agregar comentario a un post
add_post_comment:
    path: /post/{id}/comment
    controller: App\Apps\Web\Post\AddPostComment\AddPostCommentController
    methods: [PUT]
    defaults: { auth: true }

# Me gusta un post
like:
    path: /post/{id}/like
    controller: App\Apps\Web\Post\Like\LikePostController
    methods: [PUT]
    defaults: { auth: true }

# No me gusta un post
dislike:
    path: /post/{id}/dislike
    controller: App\Apps\Web\Post\Dislike\DislikePostController
    methods: [PUT]
    defaults: { auth: true }

# Eliminar un post
delete:
    path: /post/{id}/delete
    controller: App\Apps\Web\Post\Delete\DeletePostController
    methods: [DELETE]
    defaults: { auth: true }

# Obtener comentarios de un post
search_post_comments:
    path: /post/{id}/comments
    controller: App\Apps\Web\Post\SearchPostComments\SearchPostCommentsController
    methods: [GET]
    defaults: { auth: true }

# Subir recurso temporal
add_resource:
    path: /post/{id}/resource
    controller: App\Apps\Web\Post\AddPostTempResource\AddPostTemporaryResourceController
    methods: [POST]
    defaults: { auth: true }

# Obtener likes de un post
search_post_likes:
    path: /post/{id}/likes
    controller: App\Apps\Web\Post\SearchPostLikes\SearchPostLikesController
    methods: [GET]
    defaults: { auth: true }

# Obtener shares de un post
search_post_shares:
    path: /post/{id}/shares
    controller: App\Apps\Web\Post\SearchPostShares\SearchPostSharesController
    methods: [GET]
    defaults: { auth: true }

# Obtener feed del usuario actual
my_feed:
    path: /my-feed
    controller: App\Apps\Web\Post\SearchMyFeed\SearchMyFeedController
    methods: [GET]
    defaults: { auth: true }
```

### Cómo Funcionan las Rutas

**Patrón URL:**
```
https://api.example.com/api/{path}
```

El prefijo `/api` viene de:
```yaml
# config/routes.yaml
api:
  resource: './routes/web/*'
  prefix: '/api'
```

---

## Flujo Completo: Ejemplo Práctico

### Caso: Usuario crea un nuevo post

#### 1. Request HTTP

```bash
curl -X PUT "https://api.example.com/api/post/550e8400-e29b-41d4-a716-446655440000" \
  -H "Authorization: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..." \
  -H "Content-Type: application/json" \
  -d '{
    "body": "Mi primer post",
    "resources": ["image1.jpg"],
    "sharedPostId": null
  }'
```

#### 2. JWT Middleware

```
JwtAuthMiddleware::onKernelRequest()
├─ Obtiene token del header: "Authorization: eyJ..."
├─ Verifica si la ruta requiere auth: YES (defaults: { auth: true })
├─ Valida el JWT usando JwtGenerator
├─ Decodifica el payload: { "id": "user-uuid", ... }
└─ Agrega sessionId a la request: $request->attributes->set('sessionId', 'user-uuid')
```

#### 3. Controller

```php
CreatePostController::__invoke(
    Request $request,
    string $id,           // De la URL: {id}
    string $sessionId     // Del middleware: 'user-uuid'
)
├─ Crea CreatePostRequest desde HTTP:
│  └─ CreatePostRequest::fromHttp($request, $id, $sessionId)
│     ├─ Parsea JSON
│     └─ Retorna DTO con body, resources, etc.
├─ Valida el DTO: $this->validateRequest($input)
├─ Convierte a Command: $command = $input->toCommand()
│  └─ CreatePostCommand(
│       id: '550e8400-...',
│       body: 'Mi primer post',
│       resources: ['image1.jpg'],
│       sharedPostId: null,
│       userId: 'user-uuid'
│     )
├─ Dispone el Command: $this->commandBus->dispatch($command)
└─ Retorna respuesta vacía: $this->successEmptyResponse()
```

#### 4. Command Bus

```
InMemorySymfonyCommandBus::dispatch($command)
├─ Busca el manejador: CreatePostCommandHandler
└─ Ejecuta: $handler($command)
```

#### 5. Command Handler

```php
CreatePostCommandHandler::__invoke(CreatePostCommand $command)
├─ Convierte a Value Objects:
│  ├─ $id = new Uuid($command->id)
│  ├─ $body = new BodyValue($command->body)
│  └─ $user = $userRepository->findById(new Uuid($command->userId))
├─ Ejecuta la lógica de negocio:
│  └─ $this->creator->__invoke($id, $body, $user, ...)
│     ├─ Crea agregado Post::create()
│     ├─ Registra evento: PostCreatedDomainEvent
│     └─ Persiste en repositorio
└─ Publica eventos de dominio
```

#### 6. Respuesta HTTP

```json
HTTP/1.1 200 OK
Content-Length: 0
Content-Type: text/html; charset=UTF-8
```

---

### Caso: Usuario obtiene su feed

#### 1. Request HTTP

```bash
curl -X GET "https://api.example.com/api/my-feed?q=limit:10;offset:0" \
  -H "Authorization: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
```

#### 2. JWT Middleware

```
JwtAuthMiddleware::onKernelRequest()
└─ $request->attributes->set('sessionId', 'user-uuid')
```

#### 3. Controller

```php
SearchMyFeedController::__invoke(
    SearchMyFeedRequest $request,  // Inyección automática
    string $sessionId              // Del middleware
)
├─ Obtiene criterios del DTO:
│  └─ $criteria = $request->q  // ['limit' => 10, 'offset' => 0]
├─ Crea Query: $query = new SearchMyFeedQuery($sessionId, $criteria)
├─ Ejecuta Query: $response = $this->queryBus->ask($query)
└─ Retorna respuesta de colección: $this->collectionResponse($response)
```

#### 4. Query Bus

```
InMemorySymfonyQueryBus::ask($query)
├─ Busca el manejador: SearchMyFeedQueryHandler
└─ Ejecuta: $handler($query)
```

#### 5. Query Handler

```php
SearchMyFeedQueryHandler::__invoke(SearchMyFeedQuery $query)
├─ Convierte ID: $userId = new Uuid($query->id)
├─ Ejecuta búsqueda: $this->searcher->__invoke($userId, $query->criteria)
│  ├─ Busca posts del usuario en el repositorio
│  ├─ Genera URLs de recursos
│  ├─ Obtiene información del usuario propietario
│  ├─ Cuenta comentarios, likes, shares
│  └─ Retorna PostCollectionResponse
└─ Retorna respuesta
```

#### 6. Respuesta HTTP

```json
HTTP/1.1 200 OK
Content-Type: application/json

{
    "data": [
        {
            "id": "550e8400-e29b-41d4-a716-446655440000",
            "body": "Mi primer post",
            "username": "juan",
            "resources": ["https://...image1.jpg"],
            "createdAt": "2024-01-15 14:30:00",
            "urlProfileImage": "https://...profile.jpg",
            "sharedPost": null,
            "likesQuantity": 5,
            "sharesQuantity": 2,
            "commentsQuantity": 3
        }
    ],
    "metadata": {
        "limit": 10,
        "offset": 0,
        "total": 45,
        "count": 1
    }
}
```

---

## Resumen: Patrones y Buenas Prácticas

### 1. Estructura de Directorios

```
Contexts/
├── Web/
│   └── Post/
│       ├── Application/           # Lógica de aplicación
│       │   ├── Create/
│       │   │   ├── CreatePostCommand.php
│       │   │   ├── CreatePostCommandHandler.php
│       │   │   └── PostCreator.php         # Use case
│       │   ├── Find/
│       │   │   ├── FindPostQuery.php
│       │   │   ├── FindPostQueryHandler.php
│       │   │   └── PostFinder.php          # Use case
│       │   ├── Like/
│       │   ├── Delete/
│       │   ├── Search/
│       │   ├── SearchMyFeed/
│       │   └── Shared/
│       │       ├── PostResponse.php
│       │       ├── PostCollectionResponse.php
│       │       ├── PostCommentResponse.php
│       │       └── LikeResponse.php
│       ├── Domain/                # Entidades, Value Objects, Repositories
│       │   ├── Post.php
│       │   ├── Comment.php
│       │   ├── Like.php
│       │   ├── PostRepository.php (interfaz)
│       │   └── ValueObject/
│       │       ├── BodyValue.php
│       │       └── CommentValue.php
│       └── Infrastructure/        # Implementaciones técnicas
│           └── Persistence/
│               ├── MysqlPostRepository.php
│               ├── MysqlCommentRepository.php
│               └── MysqlLikeRepository.php

Apps/
└── Web/
    └── Post/
        ├── Create/
        │   ├── CreatePostController.php
        │   └── CreatePostRequest.php
        ├── Find/
        │   └── FindPostController.php
        ├── Like/
        │   └── LikePostController.php
        ├── Delete/
        │   └── DeletePostController.php
        ├── Search/
        │   ├── SearchPostsController.php
        │   └── SearchPostsRequest.php
        ├── SearchMyFeed/
        │   ├── SearchMyFeedController.php
        │   └── SearchMyFeedRequest.php
        └── AddPostComment/
            ├── AddPostCommentController.php
            └── AddPostCommentRequest.php
```

### 2. Checklist: Crear un Nuevo Endpoint

```
[ ] 1. Crear la Query/Command en Contexts/Web/Post/Application/{UseCase}/
   [ ] QueryHandler o CommandHandler
   [ ] Query o Command (si aplica)

[ ] 2. Crear los Responses en Contexts/Web/Post/Application/Shared/
   [ ] Response class con fromEntity() y toArray()

[ ] 3. Crear el Controller en Apps/Web/Post/{UseCase}/
   [ ] Extender ApiController
   [ ] Inyectar queryBus o commandBus
   [ ] Retornar respuesta apropiada

[ ] 4. Crear el Request DTO en Apps/Web/Post/{UseCase}/ (si aplica)
   [ ] Validación con atributos de Symfony Validator
   [ ] Método fromHttp() si recibe datos HTTP
   [ ] Método toQuery()/toCommand()

[ ] 5. Registrar la ruta en config/routes/web/post.yaml
   [ ] Path correcto
   [ ] Controller correcto
   [ ] Métodos HTTP
   [ ] defaults: { auth: true } si requiere autenticación

[ ] 6. Configurar la inyección de dependencias
   [ ] Implementar interfaces (Command, Query, Response)
   [ ] Usar autowiring automático o configurar en services.yaml
```

### 3. Validación

- **En Request DTOs**: Atributos de Symfony Validator
- **En Handlers**: Lógica de dominio usando Value Objects
- **En Domain**: Value Objects lanzan excepciones

### 4. Manejo de Errores

```php
// Excepciones del dominio
throw new UnauthorizedException();
throw new ExpiredTokenException();
throw new ValidationException($errors);

// El ExceptionListener las convierte a respuestas HTTP
```

### 5. Inyección de Dependencias

El proyecto usa autowiring automático:
```yaml
App\:
    resource: "../src/"
    exclude:
        - "../src/Kernel.php"
```

Todas las clases se inyectan automáticamente.

---

## Referencias Rápidas

### Commands Disponibles

| Comando | Ubicación |
|---------|-----------|
| CreatePostCommand | Contexts/Web/Post/Application/Create/ |
| LikePostCommand | Contexts/Web/Post/Application/Like/ |
| DislikePostCommand | Contexts/Web/Post/Application/Dislike/ |
| DeletePostCommand | Contexts/Web/Post/Application/Delete/ |
| AddCommentPostCommand | Contexts/Web/Post/Application/AddComment/ |

### Queries Disponibles

| Query | Ubicación |
|-------|-----------|
| FindPostQuery | Contexts/Web/Post/Application/Find/ |
| SearchPostQuery | Contexts/Web/Post/Application/Search/ |
| SearchMyFeedQuery | Contexts/Web/Post/Application/SearchMyFeed/ |
| SearchPostCommentsQuery | Contexts/Web/Post/Application/SearchPostComments/ |
| SearchPostLikesQuery | Contexts/Web/Post/Application/SearchPostLikes/ |
| SearchPostSharesQuery | Contexts/Web/Post/Application/SearchPostShares/ |

### Responses Disponibles

| Response | Ubicación |
|----------|-----------|
| PostResponse | Contexts/Web/Post/Application/Shared/ |
| PostCollectionResponse | Contexts/Web/Post/Application/Shared/ |
| PostCommentResponse | Contexts/Web/Post/Application/Shared/ |
| PostCommentCollectionResponse | Contexts/Web/Post/Application/Shared/ |
| LikeResponse | Contexts/Web/Post/Application/Shared/ |
| LikeCollectionResponse | Contexts/Web/Post/Application/Shared/ |
| ShareResponse | Contexts/Web/Post/Application/Shared/ |
| ShareCollectionResponse | Contexts/Web/Post/Application/Shared/ |

---

## Conclusión

El bounded context de Post es un excelente ejemplo de arquitectura DDD/CQRS. Para crear nuevos endpoints en otros contextos:

1. Sigue la estructura de directorios de Post
2. Crea Command/Query en Application/
3. Crea Response en Application/Shared/
4. Crea Controller en Apps/Web/
5. Crea Request DTO si es necesario
6. Registra la ruta en config/routes/web/
7. Aprovecha el autowiring y el JWT middleware existente

La arquitectura garantiza:
- Separación de responsabilidades
- Testabilidad
- Escalabilidad
- Reusabilidad de componentes
