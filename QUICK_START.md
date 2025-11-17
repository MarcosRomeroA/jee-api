# Quick Start: Bounded Context Post

Guía rápida para comenzar a trabajar con el bounded context de Post. **Leer en 5-10 minutos.**

## 3 Conceptos Clave

### 1. JWT Middleware (Autenticación)

```
HTTP Request con Authorization: eyJ...
         ↓
JwtAuthMiddleware valida token
         ↓
Extrae userId y lo agrega a la request como "sessionId"
         ↓
Controller recibe userId automáticamente
```

**Cómo activar:** En la ruta, agregar `defaults: { auth: true }`

### 2. CQRS (Command Query Responsibility Segregation)

```
Commands = Escritura
└─ CreatePostCommand → CreatePostCommandHandler → PostCreator → Persistencia

Queries = Lectura
└─ FindPostQuery → FindPostQueryHandler → PostFinder → Response
```

### 3. Inyección de Dependencias

Todas las dependencias se inyectan automáticamente en `__construct()`. No necesitas factories o singletons.

---

## Ejemplo Real: Crear un Post

### 1. Ruta (config/routes/web/post.yaml)

```yaml
create_post:
    path: /post/{id}
    controller: App\Apps\Web\Post\Create\CreatePostController
    methods: [PUT]
    defaults: { auth: true }  # ← Requiere autenticación
```

### 2. Request HTTP

```bash
curl -X PUT "http://localhost:8000/api/post/550e8400-e29b-41d4-a716-446655440000" \
  -H "Authorization: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..." \
  -H "Content-Type: application/json" \
  -d '{
    "body": "Mi primer post",
    "resources": ["image1.jpg"],
    "sharedPostId": null
  }'
```

### 3. Controller (recibe los datos)

**Archivo:** `src/Apps/Web/Post/Create/CreatePostController.php`

```php
class CreatePostController extends ApiController
{
    public function __invoke(Request $request, string $id, string $sessionId): Response
    {
        // 1. Mapear JSON a DTO
        $input = CreatePostRequest::fromHttp($request, $id, $sessionId);
        
        // 2. Validar
        $this->validateRequest($input);

        // 3. Convertir a Command
        $command = $input->toCommand();
        
        // 4. Disponer
        $this->commandBus->dispatch($command);

        // 5. Retornar
        return $this->successEmptyResponse();
    }
}
```

**Nota:** `$sessionId` viene del middleware JWT automáticamente.

### 4. Request DTO (mapea y valida)

**Archivo:** `src/Apps/Web/Post/Create/CreatePostRequest.php`

```php
final readonly class CreatePostRequest
{
    public function __construct(
        public string $id,
        public string $sessionId,

        #[Assert\NotBlank]
        #[Assert\Type("string")]
        public string $body,

        #[Assert\Type("array")]
        public array $resources = [],

        #[Assert\Type("string")]
        public ?string $sharedPostId = null,
    ) {}

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

### 5. Command (transporta datos)

**Archivo:** `src/Contexts/Web/Post/Application/Create/CreatePostCommand.php`

```php
final readonly class CreatePostCommand implements Command
{
    public function __construct(
        public string $id,
        public string $body,
        public array $resources,
        public ?string $sharedPostId,
        public string $userId,  // Del middleware
    ) {}
}
```

### 6. Handler (ejecuta el command)

**Archivo:** `src/Contexts/Web/Post/Application/Create/CreatePostCommandHandler.php`

```php
final readonly class CreatePostCommandHandler implements CommandHandler
{
    public function __construct(
        private PostCreator $creator,
        private UserRepository $userRepository,
    ) {}

    public function __invoke(CreatePostCommand $command): void
    {
        // Convertir a Value Objects (validación en constructor)
        $id = new Uuid($command->id);
        $body = new BodyValue($command->body);  // Valida: MIN=1, MAX=5000
        $user = $this->userRepository->findById(new Uuid($command->userId));

        $sharedPostId = null;
        if ($command->sharedPostId){
            $sharedPostId = new Uuid($command->sharedPostId);
        }

        // Ejecutar use case
        $this->creator->__invoke($id, $body, $user, $command->resources, $sharedPostId);
    }
}
```

### 7. Use Case (lógica de negocio)

**Archivo:** `src/Contexts/Web/Post/Application/Create/PostCreator.php`

```php
final readonly class PostCreator
{
    public function __construct(
        private PostRepository $repository,
        private PostResourceRepository $resourceRepository,
    ) {}

    public function __invoke(
        Uuid $id,
        BodyValue $body,
        User $user,
        array $resourceIds,
        ?Uuid $sharedPostId
    ): void {
        // 1. Crear agregado
        $post = Post::create($id, $body, $user, $resourceIds, $sharedPostId);

        // 2. Procesar recursos
        foreach ($resourceIds as $resourceId) {
            $resource = $this->resourceRepository->findById(new Uuid($resourceId));
            $post->addResource($resource);
        }

        // 3. Persistir
        $this->repository->save($post);
        
        // Los eventos se publican automáticamente
    }
}
```

### 8. Domain Layer (expresa reglas de negocio)

**Archivo:** `src/Contexts/Web/Post/Domain/Post.php`

```php
class Post extends AggregateRoot
{
    private Uuid $id;
    private BodyValue $body;
    private User $user;
    private ?Uuid $sharedPostId = null;
    private ?Collection $comments;
    private ?Collection $likes;

    public static function create(
        Uuid $id,
        BodyValue $body,
        User $user,
        array $resources,
        ?Uuid $sharedPostId,
    ): self {
        $post = new self($id, $body, $user, $sharedPostId);
        
        // Registrar evento de dominio
        $post->record(new PostCreatedDomainEvent($id, $resources));

        return $post;
    }
}
```

### 9. Respuesta HTTP

```
HTTP/1.1 200 OK
Content-Length: 0
```

---

## Ejemplo Real: Buscar Posts

### Request HTTP

```bash
GET "http://localhost:8000/api/posts?q=limit:10;offset:0" \
  -H "Authorization: eyJ..."
```

### Flujo Simplificado

```
Controller (SearchPostsController)
    ↓ crea
SearchPostsRequest (mapea query string)
    ↓ convierte a
SearchPostQuery (queryId, criteria)
    ↓ busca handler
QueryBus
    ↓ ejecuta
SearchPostQueryHandler (delega a use case)
    ↓ busca
PostSearcher (obtiene datos del repositorio)
    ↓ enriquece datos
PostCollectionResponse (mapea a JSON)
    ↓ retorna
HTTP 200 + JSON
```

### Respuesta HTTP

```json
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

## Checklist: Entender el Proyecto

```
[ ] Entendí que hay 3 capas:
    [ ] Apps/Web/Post/ - Controllers y Request DTOs
    [ ] Contexts/Web/Post/Application/ - Commands, Queries, Handlers, Use Cases
    [ ] Contexts/Web/Post/Domain/ - Agregados, Value Objects, Excepciones

[ ] Entendí el flujo Request → Controller → Command → Handler → Use Case

[ ] Entendí qué es:
    [ ] BodyValue - Value Object que valida el body del post
    [ ] Post - Agregado raíz del bounded context
    [ ] PostCreator - Use case que crea posts
    [ ] PostResponse - DTO que serializa en JSON

[ ] Entendí cómo funciona la autenticación JWT

[ ] Entendí qué es un Command (escribe) vs Query (lee)

[ ] Entendí que las dependencias se inyectan en __construct()
```

---

## Crear tu Primer Endpoint

### Paso 1: Definir ruta (config/routes/web/post.yaml)

```yaml
my_action:
    path: /post/{id}/my-action
    controller: App\Apps\Web\Post\MyAction\MyActionPostController
    methods: [PUT]
    defaults: { auth: true }
```

### Paso 2: Crear Command

**Archivo:** `src/Contexts/Web/Post/Application/MyAction/MyActionPostCommand.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\MyAction;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class MyActionPostCommand implements Command
{
    public function __construct(
        public string $postId,
        public string $userId,
    ) {}
}
```

### Paso 3: Crear CommandHandler

**Archivo:** `src/Contexts/Web/Post/Application/MyAction/MyActionPostCommandHandler.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\MyAction;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class MyActionPostCommandHandler implements CommandHandler
{
    public function __construct(
        private MyActionPost $executor,
    ) {}

    public function __invoke(MyActionPostCommand $command): void
    {
        $postId = new Uuid($command->postId);
        $userId = new Uuid($command->userId);
        
        $this->executor->__invoke($postId, $userId);
    }
}
```

### Paso 4: Crear Use Case

**Archivo:** `src/Contexts/Web/Post/Application/MyAction/MyActionPost.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\MyAction;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\PostRepository;
use Exception;

final readonly class MyActionPost
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
        
        // Tu lógica aquí
        
        $this->postRepository->save($post);
    }
}
```

### Paso 5: Crear Controller

**Archivo:** `src/Apps/Web/Post/MyAction/MyActionPostController.php`

```php
<?php declare(strict_types=1);

namespace App\Apps\Web\Post\MyAction;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\MyAction\MyActionPostCommand;
use Symfony\Component\HttpFoundation\Response;

final class MyActionPostController extends ApiController
{
    public function __invoke(string $id, string $sessionId): Response
    {
        $command = new MyActionPostCommand($id, $sessionId);
        $this->commandBus->dispatch($command);
        return $this->successEmptyResponse();
    }
}
```

### Listo! Tu endpoint está funcional.

Para más detalles, ver: **IMPLEMENTATION_CHECKLIST.md**

---

## Archivos Importantes

| Archivo | Propósito |
|---------|-----------|
| `config/routes/web/post.yaml` | Definir rutas |
| `src/Apps/Web/Post/{Action}/Controller.php` | Recibir request |
| `src/Contexts/Web/Post/Application/{Action}/Command.php` | Transportar datos |
| `src/Contexts/Web/Post/Application/{Action}/Handler.php` | Ejecutar command |
| `src/Contexts/Web/Post/Application/{Action}/UseCase.php` | Lógica de negocio |
| `src/Contexts/Web/Post/Domain/Post.php` | Agregado raíz |
| `src/Contexts/Web/Post/Domain/PostRepository.php` | Interfaz de persistencia |
| `src/Contexts/Shared/Infrastructure/Symfony/JwtAuthMiddleware.php` | Autenticación |
| `config/services.yaml` | Inyección de dependencias |

---

## Validación de Cambios

Después de implementar:

```bash
# Verificar sintaxis
php -l src/Apps/Web/Post/MyAction/MyActionPostController.php

# Ejecutar tests
./bin/phpunit tests/Feature/Apps/Web/Post/MyAction/

# Probar manualmente
curl -X PUT http://localhost:8000/api/post/id/my-action \
  -H "Authorization: eyJ..." \
  -d '{}'
```

---

## Errores Comunes

### Error: "No middleware for JWT"
**Causa:** Olvidaste `defaults: { auth: true }` en la ruta

**Solución:**
```yaml
my_route:
    defaults: { auth: true }  # ← Agregar esto
```

### Error: "sessionId siempre es null"
**Causa:** El JWT del Authorization header es inválido

**Solución:** Generar un JWT válido antes

### Error: "Command no se ejecuta"
**Causa:** Handler no implementa CommandHandler o método no es `__invoke()`

**Solución:** Verificar interfaz e nombre de método

---

## Próximos Pasos

1. **Entender Value Objects:**
   - Abre `src/Contexts/Web/Post/Domain/ValueObject/BodyValue.php`
   - Ve cómo valida en el constructor

2. **Entender Domain Events:**
   - Abre `src/Contexts/Web/Post/Domain/Events/PostCreatedDomainEvent.php`
   - Lee cómo se registran en agregados

3. **Entender Repositories:**
   - Abre `src/Contexts/Web/Post/Infrastructure/Persistence/MysqlPostRepository.php`
   - Ve cómo se implementan búsquedas complejas

4. **Escribir Tests:**
   - Abre `tests/Feature/Apps/Web/Post/Create/`
   - Copia y adapta para tu endpoint

---

## Recursos

- **Documentación completa:** Ver archivos `.md` en la raíz del proyecto
- **Patrón:** Domain-Driven Design + CQRS
- **Preguntas:** Ver POST_CONTEXT_FILE_REFERENCE.md → "Búsqueda Rápida"

---

**¡Ya estás listo para contribuir al bounded context de Post!**

Documentos recomendados a leer después:
1. BOUNDED_CONTEXT_POST_GUIDE.md (entender profundamente)
2. POST_CONTEXT_ADVANCED_PATTERNS.md (patrones complejos)
3. CODE_TEMPLATES.md (plantillas listas)
