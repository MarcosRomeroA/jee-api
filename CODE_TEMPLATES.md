# Plantillas de Código: Listas para Copiar-Pegar

Documento con plantillas de código listas para copiar y adaptar a nuevos endpoints.

## Tabla de Contenidos

1. [Command](#command)
2. [CommandHandler](#commandhandler)
3. [Use Case](#use-case)
4. [Query](#query)
5. [QueryHandler](#queryhandler)
6. [Controller para Command](#controller-para-command)
7. [Controller para Query](#controller-para-query)
8. [Request DTO Simple](#request-dto-simple)
9. [Request DTO con BaseRequest](#request-dto-con-baserequest)
10. [Response Individual](#response-individual)
11. [Response Colección](#response-colección)
12. [Domain Event](#domain-event)
13. [Domain Exception](#domain-exception)
14. [Ruta YAML](#ruta-yaml)

---

## Command

**Ubicación:** `src/Contexts/Web/Post/Application/{UseCase}/{Action}PostCommand.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\{UseCase};

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class {Action}PostCommand implements Command
{
    public function __construct(
        public string $postId,
        public string $userId,
        // Agregar parámetros adicionales según sea necesario
        // public string $someData,
        // public ?string $optionalData = null,
    )
    {
    }
}
```

**Reemplazar:**
- `{UseCase}`: Nombre del use case (Share, Archive, etc.)
- `{Action}`: Acción en infinitivo (Share, Archive, etc.)

**Ejemplo concreto:**
```php
// Para "Compartir un post"
// Ubicación: src/Contexts/Web/Post/Application/Share/SharePostCommand.php

namespace App\Contexts\Web\Post\Application\Share;

final readonly class SharePostCommand implements Command
{
    public function __construct(
        public string $postId,
        public string $userId,
    ) {}
}
```

---

## CommandHandler

**Ubicación:** `src/Contexts/Web/Post/Application/{UseCase}/{Action}PostCommandHandler.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\{UseCase};

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class {Action}PostCommandHandler implements CommandHandler
{
    public function __construct(
        private {Action}Post ${camelCaseAction},
    )
    {
    }

    public function __invoke({Action}PostCommand $command): void
    {
        $postId = new Uuid($command->postId);
        $userId = new Uuid($command->userId);
        
        $this->{camelCaseAction}->__invoke($postId, $userId);
    }
}
```

**Reemplazar:**
- `{UseCase}`: Nombre del use case
- `{Action}`: Acción
- `{camelCaseAction}`: Action en camelCase (share → share, archive → archive)

**Ejemplo concreto:**
```php
// Para "Compartir un post"
namespace App\Contexts\Web\Post\Application\Share;

final readonly class SharePostCommandHandler implements CommandHandler
{
    public function __construct(
        private SharePost $share,
    ) {}

    public function __invoke(SharePostCommand $command): void
    {
        $postId = new Uuid($command->postId);
        $userId = new Uuid($command->userId);
        
        $this->share->__invoke($postId, $userId);
    }
}
```

---

## Use Case

**Ubicación:** `src/Contexts/Web/Post/Application/{UseCase}/{Action}Post.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\{UseCase};

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\PostRepository;
use Exception;

final readonly class {Action}Post
{
    public function __construct(
        private PostRepository $postRepository,
        // Agregar más repositorios/servicios según sea necesario
        // private CommentRepository $commentRepository,
    )
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke(Uuid $postId, Uuid $userId): void
    {
        // 1. Obtener datos necesarios
        $post = $this->postRepository->findById($postId);

        // 2. Validar reglas de negocio
        // if ($post->getUser()->getId() !== $userId) {
        //     throw new NotAuthorizedToModifyPostException(...);
        // }

        // 3. Aplicar cambios
        // $post->markAsArchived();
        // $post->recordThat(new Post{Action}DomainEvent($postId, $userId));

        // 4. Persistir
        $this->postRepository->save($post);
    }
}
```

**Reemplazar:**
- `{UseCase}`: Nombre del use case
- `{Action}`: Acción
- Ajustar repositorios y lógica

**Ejemplo concreto:**
```php
// Para "Compartir un post"
namespace App\Contexts\Web\Post\Application\Share;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\PostRepository;
use Exception;

final readonly class SharePost
{
    public function __construct(
        private PostRepository $postRepository,
    ) {}

    /**
     * @throws Exception
     */
    public function __invoke(Uuid $postId, Uuid $userId): void
    {
        $post = $this->postRepository->findById($postId);

        if ($post->getSharedPostId() !== null) {
            throw new CannotShareASharedPostException(
                "No se puede compartir un post que ya es un share"
            );
        }

        $sharedPost = Post::create(
            new Uuid(uniqid()),
            $post->getBody(),
            $this->userRepository->findById($userId),
            [],
            $postId
        );

        $sharedPost->recordThat(
            new PostSharedDomainEvent($postId, $userId)
        );

        $this->postRepository->save($sharedPost);
    }
}
```

---

## Query

**Ubicación:** `src/Contexts/Web/Post/Application/{UseCase}/{Action}PostQuery.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\{UseCase};

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class {Action}PostQuery implements Query
{
    public function __construct(
        public string $postId,
        // Agregar parámetros adicionales
        // public ?array $criteria = null,
    )
    {
    }
}
```

**Reemplazar:**
- `{UseCase}`: Nombre del use case
- `{Action}`: Acción

---

## QueryHandler

**Ubicación:** `src/Contexts/Web/Post/Application/{UseCase}/{Action}PostQueryHandler.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\{UseCase};

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Application\Shared\PostResponse;
use Exception;

final readonly class {Action}PostQueryHandler implements QueryHandler
{
    public function __construct(
        private {Action}Post $finder,
    )
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke({Action}PostQuery $query): PostResponse
    {
        $postId = new Uuid($query->postId);

        return $this->finder->__invoke($postId);
    }
}
```

**Reemplazar:**
- `{UseCase}`: Nombre del use case
- `{Action}`: Acción
- Ajustar Response según sea necesario

---

## Controller para Command

**Ubicación:** `src/Apps/Web/Post/{UseCase}/{Action}PostController.php`

```php
<?php declare(strict_types=1);

namespace App\Apps\Web\Post\{UseCase};

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\{UseCase}\{Action}PostCommand;
use Symfony\Component\HttpFoundation\Response;

final class {Action}PostController extends ApiController
{
    public function __invoke(
        string $id,
        string $sessionId
    ): Response
    {
        $command = new {Action}PostCommand(
            $id,
            $sessionId
            // Agregar más parámetros si es necesario
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
```

**Reemplazar:**
- `{UseCase}`: Nombre del use case
- `{Action}`: Acción

**Variante: Con Request DTO**

```php
<?php declare(strict_types=1);

namespace App\Apps\Web\Post\{UseCase};

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\{UseCase}\{Action}PostCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class {Action}PostController extends ApiController
{
    public function __invoke(
        Request $request,
        string $id,
        string $sessionId
    ): Response
    {
        $input = {Action}PostRequest::fromHttp($request, $id, $sessionId);
        $this->validateRequest($input);

        $command = $input->toCommand();
        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
```

---

## Controller para Query

**Ubicación:** `src/Apps/Web/Post/{UseCase}/{Action}PostController.php`

```php
<?php declare(strict_types=1);

namespace App\Apps\Web\Post\{UseCase};

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\{UseCase}\{Action}PostQuery;
use Symfony\Component\HttpFoundation\Response;

final class {Action}PostController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $query = new {Action}PostQuery($id);

        $response = $this->queryBus->ask($query);

        return $this->successResponse($response);
    }
}
```

---

## Request DTO Simple

**Ubicación:** `src/Apps/Web/Post/{UseCase}/{Action}PostRequest.php`

```php
<?php declare(strict_types=1);

namespace App\Apps\Web\Post\{UseCase};

use App\Contexts\Web\Post\Application\{UseCase}\{Action}PostCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class {Action}PostRequest
{
    public function __construct(
        public string $id,
        public string $sessionId,

        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $someField,

        #[Assert\Type("array")]
        public array $anotherField = [],

        #[Assert\Type("string")]
        public ?string $optionalField = null,
    ) {}

    public static function fromHttp(Request $request, string $id, string $sessionId): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            $id,
            $sessionId,
            $data['someField'] ?? '',
            $data['anotherField'] ?? [],
            $data['optionalField'] ?? null
        );
    }

    public function toCommand(): {Action}PostCommand
    {
        return new {Action}PostCommand(
            $this->id,
            $this->sessionId,
            $this->someField,
            $this->anotherField,
            $this->optionalField
        );
    }
}
```

---

## Request DTO con BaseRequest

**Ubicación:** `src/Apps/Web/Post/{UseCase}/{Action}PostRequest.php`

```php
<?php declare(strict_types=1);

namespace App\Apps\Web\Post\{UseCase};

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class {Action}PostRequest extends BaseRequest
{
    #[Assert\NotNull]
    #[Assert\Type("string")]
    public mixed $field1;

    #[Assert\NotNull]
    #[Assert\Type("string")]
    public mixed $field2;
}
```

---

## Response Individual

**Ubicación:** `src/Contexts/Web/Post/Application/Shared/{Entity}Response.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Post\Domain\{Entity};

final class {Entity}Response extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $field1,
        public readonly string $field2,
        public readonly ?string $optionalField,
    )
    {
    }

    public static function fromEntity({Entity} ${camelCaseEntity}): self
    {
        return new self(
            ${camelCaseEntity}->getId()->value(),
            ${camelCaseEntity}->getField1()->value(),
            ${camelCaseEntity}->getField2()->value(),
            ${camelCaseEntity}->getOptionalField(),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
```

**Reemplazar:**
- `{Entity}`: Nombre de la entidad
- `${camelCaseEntity}`: Entidad en camelCase

---

## Response Colección

**Ubicación:** `src/Contexts/Web/Post/Application/Shared/{Entity}CollectionResponse.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Post\Domain\{Entity};

class {Entity}CollectionResponse extends Response
{
    /** @var {Entity}[] */
    public array ${camelCaseEntity}s;
    public int $limit;
    public int $offset;
    public int $total;

    /**
     * @param array<{Entity}> ${camelCaseEntity}s
     * @param array{limit: int, offset: int} $criteria
     * @param int $total
     */
    public function __construct(array ${camelCaseEntity}s, array $criteria, int $total = 0)
    {
        $this->{camelCaseEntity}s = ${camelCaseEntity}s;
        $this->limit = $criteria["limit"];
        $this->offset = $criteria["offset"];
        $this->total = $total;
    }

    public function toArray(): array
    {
        $response = [];

        foreach($this->{camelCaseEntity}s as ${camelCaseEntity}){
            $response[] = {Entity}Response::fromEntity(${camelCaseEntity})->toArray();
        }

        return [
            'data' => $response,
            'metadata' => [
                'limit' => $this->limit,
                'offset' => $this->offset,
                'total' => $this->total,
                'count' => count($this->{camelCaseEntity}s)
            ]
        ];
    }
}
```

---

## Domain Event

**Ubicación:** `src/Contexts/Web/Post/Domain/Events/Post{Action}DomainEvent.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Events;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class Post{Action}DomainEvent implements DomainEvent
{
    public function __construct(
        public Uuid $postId,
        public Uuid $userId,
        // Agregar datos adicionales si es necesario
    )
    {
    }

    public static function eventName(): string
    {
        return 'post.{camelCaseAction}';  // post.shared, post.archived, etc.
    }

    public function aggregateId(): Uuid
    {
        return $this->postId;
    }
}
```

---

## Domain Exception

**Ubicación:** `src/Contexts/Web/Post/Domain/Exception/{ExceptionName}Exception.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Exception;

final class {ExceptionName}Exception extends PostDomainException
{
    // Usar cuando: ...
}
```

**Ejemplo concreto:**

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Exception;

final class CannotShareASharedPostException extends PostDomainException
{
    // Usar cuando: Un usuario intenta compartir un post que ya es un share
}
```

---

## Ruta YAML

**Ubicación:** `config/routes/web/post.yaml`

```yaml
# Acción que modifica estado (Command)
action_verb:
    path: /post/{id}/{action}
    controller: App\Apps\Web\Post\{UseCase}\{Action}PostController
    methods: [PUT]  # o POST, DELETE, etc.
    defaults: { auth: true }

# Acción que lee datos (Query)
search_entity:
    path: /posts/{id}/sub-resources
    controller: App\Apps\Web\Post\{UseCase}\{Action}PostController
    methods: [GET]
    defaults: { auth: true }
```

**Ejemplo concreto:**

```yaml
# Compartir un post
share_post:
    path: /post/{id}/share
    controller: App\Apps\Web\Post\Share\SharePostController
    methods: [PUT]
    defaults: { auth: true }

# Obtener posts archivados
search_archived:
    path: /posts/archived
    controller: App\Apps\Web\Post\SearchArchived\SearchArchivedPostsController
    methods: [GET]
    defaults: { auth: true }
```

---

## Ejemplo Completo: Endpoint de Compartir Post

### 1. Ruta (config/routes/web/post.yaml)

```yaml
share_post:
    path: /post/{id}/share
    controller: App\Apps\Web\Post\Share\SharePostController
    methods: [PUT]
    defaults: { auth: true }
```

### 2. Command

**Archivo:** `src/Contexts/Web/Post/Application/Share/SharePostCommand.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Share;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class SharePostCommand implements Command
{
    public function __construct(
        public string $postId,
        public string $userId,
    ) {}
}
```

### 3. CommandHandler

**Archivo:** `src/Contexts/Web/Post/Application/Share/SharePostCommandHandler.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Share;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class SharePostCommandHandler implements CommandHandler
{
    public function __construct(
        private SharePost $share,
    ) {}

    public function __invoke(SharePostCommand $command): void
    {
        $postId = new Uuid($command->postId);
        $userId = new Uuid($command->userId);
        
        $this->share->__invoke($postId, $userId);
    }
}
```

### 4. Use Case

**Archivo:** `src/Contexts/Web/Post/Application/Share/SharePost.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Share;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\PostRepository;
use App\Contexts\Web\Post\Domain\Events\PostSharedDomainEvent;
use App\Contexts\Web\Post\Domain\Post;
use App\Contexts\Web\User\Domain\UserRepository;
use Exception;

final readonly class SharePost
{
    public function __construct(
        private PostRepository $postRepository,
        private UserRepository $userRepository,
    ) {}

    /**
     * @throws Exception
     */
    public function __invoke(Uuid $postId, Uuid $userId): void
    {
        $post = $this->postRepository->findById($postId);

        if ($post->getSharedPostId() !== null) {
            throw new CannotShareASharedPostException(
                "No se puede compartir un post que ya es un share"
            );
        }

        $user = $this->userRepository->findById($userId);

        $sharedPost = Post::create(
            new Uuid(uniqid()),
            $post->getBody(),
            $user,
            [],
            $postId
        );

        $sharedPost->recordThat(
            new PostSharedDomainEvent($postId, $userId)
        );

        $this->postRepository->save($sharedPost);
    }
}
```

### 5. Controller

**Archivo:** `src/Apps/Web/Post/Share/SharePostController.php`

```php
<?php declare(strict_types=1);

namespace App\Apps\Web\Post\Share;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\Share\SharePostCommand;
use Symfony\Component\HttpFoundation\Response;

final class SharePostController extends ApiController
{
    public function __invoke(string $id, string $sessionId): Response
    {
        $command = new SharePostCommand(
            $id,
            $sessionId
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
```

### 6. Domain Event

**Archivo:** `src/Contexts/Web/Post/Domain/Events/PostSharedDomainEvent.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Events;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class PostSharedDomainEvent implements DomainEvent
{
    public function __construct(
        public Uuid $originalPostId,
        public Uuid $userId,
    ) {}

    public static function eventName(): string
    {
        return 'post.shared';
    }

    public function aggregateId(): Uuid
    {
        return $this->originalPostId;
    }
}
```

### 7. Domain Exception

**Archivo:** `src/Contexts/Web/Post/Domain/Exception/CannotShareASharedPostException.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Exception;

final class CannotShareASharedPostException extends PostDomainException {}
```

---

## Test Template

**Ubicación:** `tests/Feature/Apps/Web/Post/{UseCase}/{Action}PostControllerTest.php`

```php
<?php declare(strict_types=1);

namespace Tests\Feature\Apps\Web\Post\{UseCase};

use ApiTestCase;

class {Action}PostControllerTest extends ApiTestCase
{
    public function test{Action}PostWithValidData(): void
    {
        // Setup
        $user = $this->fixtures->createUser();
        $post = $this->fixtures->createPost('Mi post', $user);
        $anotherUser = $this->fixtures->createUser();
        $token = $this->generateJWT($anotherUser->getId()->value());

        // Request
        $response = $this->client->request('PUT', 
            '/api/post/' . $post->getId()->value() . '/{action}',
            ['headers' => ['Authorization' => $token]]
        );

        // Verificaciones
        $this->assertEquals(200, $response->getStatusCode());
        // Agregar verificaciones adicionales según sea necesario
    }

    public function test{Action}PostWithoutAuthenticationThrows(): void
    {
        $post = $this->fixtures->createPost('Mi post');

        $response = $this->client->request('PUT', 
            '/api/post/' . $post->getId()->value() . '/{action}'
        );

        $this->assertEquals(401, $response->getStatusCode());
    }
}
```

---

## Consejos para Usar las Plantillas

1. **Copia completa**: Copia todas las líneas
2. **Reemplaza placeholders**: `{UseCase}`, `{Action}`, etc.
3. **Adapta la lógica**: Ajusta según tus necesidades
4. **Mantén la estructura**: No cambies la arquitectura general
5. **Sigue convenciones**: Nombres en camelCase, PascalCase, etc.

## Validación Rápida

Después de copiar-pegar, verifica:

```bash
# Sintaxis PHP
php -l src/Apps/Web/Post/NewFeature/Controller.php

# Namespace correcto
grep -n "^namespace" src/Apps/Web/Post/NewFeature/Controller.php

# Interfaces implementadas
grep "implements" src/Apps/Web/Post/NewFeature/Controller.php

# Métodos __invoke
grep "public function __invoke" src/Apps/Web/Post/NewFeature/Controller.php
```
