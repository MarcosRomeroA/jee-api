# Checklist de Implementación: Nuevo Endpoint

Esta guía te lleva paso a paso a través de la creación de un nuevo endpoint siguiendo los patrones del bounded context de Post.

## Ejemplo: Crear Endpoint "Compartir Post"

Vamos a crear un endpoint para que un usuario pueda compartir un post.

```
Ruta: PUT /api/post/{id}/share
Autenticación: Requerida
Datos de entrada: ninguno (usa datos de la URL y sesión)
Respuesta: 200 OK (vacía)
```

---

## Paso 1: Definir la Ruta

**Archivo:** `config/routes/web/post.yaml`

```yaml
share_post:
    path: /post/{id}/share
    controller: App\Apps\Web\Post\Share\SharePostController
    methods: [PUT]
    defaults: { auth: true }  # IMPORTANTE: Requiere autenticación
```

Adiciones:
- `path`: La URL que se llamará (relativa a `/api`)
- `controller`: La clase que manejará la request
- `methods`: [PUT] para modificar datos
- `defaults: { auth: true }`: Activa validación JWT

---

## Paso 2: Crear la entidad Command

**Ubicación:** `src/Contexts/Web/Post/Application/Share/SharePostCommand.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Share;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class SharePostCommand implements Command
{
    public function __construct(
        public string $postId,
        public string $userId,
    )
    {
    }
}
```

Detalles:
- Implementa interfaz `Command`
- `readonly` = inmutable
- Solo contiene datos, sin lógica
- El `userId` viene del middleware JWT

---

## Paso 3: Crear el Handler del Command

**Ubicación:** `src/Contexts/Web/Post/Application/Share/SharePostCommandHandler.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Share;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class SharePostCommandHandler implements CommandHandler
{
    public function __construct(
        private PostSharer $sharer,
    )
    {
    }

    public function __invoke(SharePostCommand $command): void
    {
        $postId = new Uuid($command->postId);
        $userId = new Uuid($command->userId);
        
        $this->sharer->__invoke($postId, $userId);
    }
}
```

Detalles:
- Implementa interfaz `CommandHandler`
- Recibe el Command
- Convierte strings a Value Objects
- Delega a PostSharer (use case)

---

## Paso 4: Crear el Use Case

**Ubicación:** `src/Contexts/Web/Post/Application/Share/PostSharer.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Share;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\PostRepository;
use Exception;

final readonly class PostSharer
{
    public function __construct(
        private PostRepository $postRepository,
    )
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke(Uuid $postId, Uuid $userId): void
    {
        // 1. Obtener el post original
        $post = $this->postRepository->findById($postId);

        // 2. Verificar que no sea un post compartido previamente
        if ($post->getSharedPostId() !== null) {
            throw new CannotShareASharedPostException(
                "No se puede compartir un post que ya es un share"
            );
        }

        // 3. Crear un nuevo post con referencia al original
        $sharedPost = Post::create(
            new Uuid(uniqid()),  // Nuevo ID
            $post->getBody(),    // Mismo cuerpo
            $this->userRepository->findById($userId),  // Usuario actual
            [],                   // Sin recursos nuevos
            $postId              // Post original
        );

        // 4. Registrar evento
        $sharedPost->recordThat(
            new PostSharedDomainEvent($postId, $userId)
        );

        // 5. Persistir
        $this->postRepository->save($sharedPost);
    }
}
```

Detalles:
- `PostSharer` es el use case
- Contiene toda la lógica de negocio
- Inyecta repositorios como dependencias
- Lanza excepciones de dominio si hay problemas

---

## Paso 5: Crear el Controller

**Ubicación:** `src/Apps/Web/Post/Share/SharePostController.php`

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
        // 1. Crear Command
        $command = new SharePostCommand(
            $id,          // De la URL {id}
            $sessionId    // Del middleware JWT
        );

        // 2. Disponer el Command
        $this->commandBus->dispatch($command);

        // 3. Retornar respuesta vacía (201 Created o 200 OK)
        return $this->successEmptyResponse();
    }
}
```

Detalles:
- Extiende `ApiController`
- `$id` viene de la URL: `{id}`
- `$sessionId` viene del middleware JWT
- Muy simple: solo crea Command y lo dispone

---

## Paso 6: Crear Exception de Dominio (si es necesario)

**Ubicación:** `src/Contexts/Web/Post/Domain/Exception/CannotShareASharedPostException.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Exception;

final class CannotShareASharedPostException extends PostDomainException
{
    // Lanzar cuando se intenta compartir un post ya compartido
}
```

Detalles:
- Extiende `PostDomainException`
- Representa una regla de negocio violada
- Se mapea automáticamente a HTTP status code

---

## Paso 7: Crear Domain Event (si es necesario)

**Ubicación:** `src/Contexts/Web/Post/Domain/Events/PostSharedDomainEvent.php`

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
    )
    {
    }

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

Detalles:
- Implementa `DomainEvent`
- Se registra en el agregado con `recordThat()`
- Otros bounded contexts pueden escucharlo

---

## Paso 8: Crear Subscriber si es necesario

Si necesitas reaccionar al evento (p.ej., enviar notificación):

**Ubicación:** `src/Contexts/Web/Post/Application/Share/PostSharedSubscriber.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Share;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Web\Post\Domain\Events\PostSharedDomainEvent;

final readonly class PostSharedSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private NotificationService $notificationService,
    )
    {
    }

    public static function subscribedTo(): string
    {
        return PostSharedDomainEvent::eventName();
    }

    public function __invoke(PostSharedDomainEvent $event): void
    {
        // Enviar notificación al propietario del post original
        $this->notificationService->notifyPostShared(
            $event->originalPostId,
            $event->userId
        );
    }
}
```

Detalles:
- Implementa `DomainEventSubscriber`
- Se ejecuta automáticamente cuando el evento se publica
- Se registra en services.yaml automáticamente

---

## Paso 9: Tests (Recomendado)

### Test del Handler

**Ubicación:** `tests/Feature/Apps/Web/Post/Share/SharePostControllerTest.php`

```php
<?php declare(strict_types=1);

namespace Tests\Feature\Apps\Web\Post\Share;

use ApiTestCase;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

class SharePostControllerTest extends ApiTestCase
{
    public function testSharePostWithValidData(): void
    {
        // Setup: crear usuario y post
        $user = $this->fixtures->createUser();
        $post = $this->fixtures->createPost('Mi post', $user);
        $anotherUser = $this->fixtures->createUser();
        $token = $this->generateJWT($anotherUser->getId()->value());

        // Request: compartir post
        $response = $this->client->request('PUT', 
            '/api/post/' . $post->getId()->value() . '/share',
            [
                'headers' => ['Authorization' => $token]
            ]
        );

        // Verificaciones
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(
            $this->fixtures->postIsSharedBy(
                $post->getId()->value(),
                $anotherUser->getId()->value()
            )
        );
    }

    public function testSharePostTwiceThrows(): void
    {
        // Setup
        $user = $this->fixtures->createUser();
        $post = $this->fixtures->createPost('Mi post', $user);
        $token = $this->generateJWT($user->getId()->value());

        // Primer share: OK
        $this->client->request('PUT', 
            '/api/post/' . $post->getId()->value() . '/share',
            ['headers' => ['Authorization' => $token]]
        );

        // Segundo share: error
        $response = $this->client->request('PUT', 
            '/api/post/' . $post->getId()->value() . '/share',
            ['headers' => ['Authorization' => $token]]
        );

        // Debería retornar error 409 Conflict
        $this->assertEquals(409, $response->getStatusCode());
    }

    public function testSharePostWithoutAuthenticationThrows(): void
    {
        $post = $this->fixtures->createPost('Mi post');

        $response = $this->client->request('PUT', 
            '/api/post/' . $post->getId()->value() . '/share'
        );

        $this->assertEquals(401, $response->getStatusCode());
    }
}
```

---

## Paso 10: Verificar la Estructura

```bash
# Desde la raíz del proyecto
ls -la src/Apps/Web/Post/Share/
# Debe contener: SharePostController.php

ls -la src/Contexts/Web/Post/Application/Share/
# Debe contener:
# - SharePostCommand.php
# - SharePostCommandHandler.php
# - PostSharer.php
# - PostSharedSubscriber.php (opcional)

ls -la src/Contexts/Web/Post/Domain/Events/
# Debe contener: PostSharedDomainEvent.php

ls -la src/Contexts/Web/Post/Domain/Exception/
# Debe contener: CannotShareASharedPostException.php
```

---

## Checklist Final

```
[ ] 1. Ruta registrada en config/routes/web/post.yaml
       [ ] Path correcto
       [ ] Controller correcto
       [ ] Métodos HTTP
       [ ] auth: true (si requiere autenticación)

[ ] 2. Command creado y funcional
       [ ] Implementa Command interface
       [ ] Es readonly
       [ ] Contiene solo datos

[ ] 3. CommandHandler implementado
       [ ] Implementa CommandHandler interface
       [ ] Convierte a Value Objects
       [ ] Delega a use case
       [ ] Método __invoke(Command): void

[ ] 4. Use Case creado
       [ ] Contiene toda la lógica de negocio
       [ ] Usa repositorios
       [ ] Valida reglas de negocio
       [ ] Registra eventos si es necesario
       [ ] Persiste datos

[ ] 5. Controller implementado
       [ ] Extiende ApiController
       [ ] Recibe parámetros correctos
       [ ] Crea Command/Query
       [ ] Dispone/ejecuta
       [ ] Retorna respuesta apropiada

[ ] 6. Domain Event creado (si aplica)
       [ ] Implementa DomainEvent interface
       [ ] Tiene eventName() estático
       [ ] Se registra en el agregado

[ ] 7. Exception creada (si aplica)
       [ ] Extiende PostDomainException
       [ ] Se lanza en el use case

[ ] 8. Subscriber creado (si aplica)
       [ ] Implementa DomainEventSubscriber
       [ ] Escucha el evento correcto
       [ ] Contiene lógica reactiva

[ ] 9. Tests escritos
       [ ] Happy path
       [ ] Error cases
       [ ] Validación de seguridad

[ ] 10. Documentación
        [ ] Comentarios en código
        [ ] En postman o swagger
        [ ] En README si es público

[ ] 11. Verificación manual
        [ ] curl/postman request
        [ ] Verificar respuesta
        [ ] Verificar BD
        [ ] Verificar eventos si aplica
```

---

## Plantilla Rápida para Copiar-Pegar

### Command
```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\{UseCase};

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class {Action}PostCommand implements Command
{
    public function __construct(
        public string $postId,
        public string $userId,
        // Agregar más parámetros según sea necesario
    )
    {
    }
}
```

### CommandHandler
```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\{UseCase};

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class {Action}PostCommandHandler implements CommandHandler
{
    public function __construct(
        private {Action}Post $handler,
    )
    {
    }

    public function __invoke({Action}PostCommand $command): void
    {
        $postId = new Uuid($command->postId);
        $userId = new Uuid($command->userId);
        
        $this->handler->__invoke($postId, $userId);
    }
}
```

### Use Case
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
    )
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke(Uuid $postId, Uuid $userId): void
    {
        // 1. Obtener el post
        $post = $this->postRepository->findById($postId);

        // 2. Validar reglas de negocio

        // 3. Actualizar estado del post

        // 4. Registrar eventos si es necesario
        // $post->recordThat(new PostXxxDomainEvent(...));

        // 5. Persistir
        $this->postRepository->save($post);
    }
}
```

### Controller
```php
<?php declare(strict_types=1);

namespace App\Apps\Web\Post\{UseCase};

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\{UseCase}\{Action}PostCommand;
use Symfony\Component\HttpFoundation\Response;

final class {Action}PostController extends ApiController
{
    public function __invoke(string $id, string $sessionId): Response
    {
        $command = new {Action}PostCommand(
            $id,
            $sessionId
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}
```

---

## Flujo de Ejecución Resumido

```
1. Request HTTP llega a /api/post/{id}/share
   └─ Middleware JWT valida token y extrae sessionId

2. SharePostController::__invoke(string $id, string $sessionId)
   ├─ Crea SharePostCommand(id, sessionId)
   └─ Dispone: $this->commandBus->dispatch($command)

3. CommandBus busca SharePostCommandHandler
   └─ Ejecuta: $handler->__invoke($command)

4. SharePostCommandHandler::__invoke()
   ├─ Convierte strings a Uuid
   └─ Ejecuta: $this->sharer->__invoke($postId, $userId)

5. PostSharer::__invoke(Uuid $postId, Uuid $userId)
   ├─ Obtiene post del repositorio
   ├─ Valida reglas de negocio
   ├─ Crea nuevo post compartido
   ├─ Registra evento: PostSharedDomainEvent
   └─ Persiste con $postRepository->save()

6. Evento se publica automáticamente
   └─ PostSharedSubscriber escucha y ejecuta lógica reactiva

7. Controller retorna respuesta
   └─ return $this->successEmptyResponse() → HTTP 200
```

---

## Troubleshooting

### El controller no recibe sessionId

**Problema:** El parámetro `string $sessionId` siempre es null

**Solución:** Asegúrate que la ruta tiene `defaults: { auth: true }`

```yaml
share_post:
    path: /post/{id}/share
    controller: App\Apps\Web\Post\Share\SharePostController
    methods: [PUT]
    defaults: { auth: true }  # ← NECESARIO
```

### El CommandHandler no se ejecuta

**Problema:** Dispongo el command pero no hace nada

**Solución:** Verifica que:
1. La clase implementa `CommandHandler`
2. Tiene método `__invoke(Command)`
3. El nombre del handler sigue el patrón: `{Command}Handler`

```php
// ✓ Correcto
final readonly class SharePostCommandHandler implements CommandHandler {
    public function __invoke(SharePostCommand $command): void { }
}

// ✗ Incorrecto
final readonly class ShareHandler {  // No implementa interfaz
    public function handle(SharePostCommand $command) { }  // Método mal nombrado
}
```

### Las excepciones no se convierten a HTTP responses

**Problema:** Lanzo una excepción pero obtengo 500 en lugar del código esperado

**Solución:** La excepción debe estar en el mapeo de `ApiExceptionsHttpStatusCodeMapping`

```php
// En ApiExceptionsHttpStatusCodeMapping
const MAPPING = [
    // ...
    CannotShareASharedPostException::class => 409,  // ← Agregar aquí
];
```

---

## Conclusión

Sigue este checklist y tendrás un endpoint completamente funcional siguiendo los patrones de la arquitectura DDD/CQRS del proyecto.

Los puntos clave son:
1. **Separación de responsabilidades**: Cada clase tiene una única responsabilidad
2. **Testabilidad**: Todo es inyectable y mockeable
3. **Mantenibilidad**: El flujo es claro y predecible
4. **Escalabilidad**: Los patrones se repiten en todos los endpoints
