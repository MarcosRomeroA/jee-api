# Patrones Avanzados: Bounded Context Post

Documento complementario con patrones avanzados, use cases complejos y mejores prácticas implementadas en el bounded context de Post.

## Tabla de Contenidos

1. [Value Objects](#value-objects)
2. [Domain Events](#domain-events)
3. [Repositories y Búsqueda](#repositories-y-búsqueda)
4. [Use Cases Complejos](#use-cases-complejos)
5. [Validación Multinivel](#validación-multinivel)
6. [Handling de Recursos](#handling-de-recursos)
7. [Excepciones de Dominio](#excepciones-de-dominio)
8. [Testabilidad](#testabilidad)

---

## Value Objects

Los Value Objects encapsulan validación y lógica de transformación de datos primitivos.

### BodyValue - Validación de String

**Ubicación:** `src/Contexts/Web/Post/Domain/ValueObject/BodyValue.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\ValueObject;

final readonly class BodyValue
{
    private const MIN_LENGTH = 1;
    private const MAX_LENGTH = 5000;

    public function __construct(private string $value)
    {
        $this->validate();
    }

    private function validate(): void
    {
        // Validación de largo mínimo
        if (strlen($this->value) < self::MIN_LENGTH) {
            throw new BodyTooShortException(
                "El body debe tener al menos " . self::MIN_LENGTH . " caracteres"
            );
        }

        // Validación de largo máximo
        if (strlen($this->value) > self::MAX_LENGTH) {
            throw new TextIsLongerThanAllowedException(
                "El body no puede exceder " . self::MAX_LENGTH . " caracteres"
            );
        }

        // Validación de contenido
        if (trim($this->value) === '') {
            throw new BodyCannotBeEmptyException(
                "El body no puede contener solo espacios en blanco"
            );
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(BodyValue $other): bool
    {
        return $this->value === $other->value;
    }

    public function length(): int
    {
        return strlen($this->value);
    }
}
```

**Uso en Domain:**
```php
// En CreatePostCommandHandler
$body = new BodyValue($command->body);  // Lanza excepción si no es válido

// En POST request JSON
{
    "body": "Mi primer post"  // Mínimo 1, máximo 5000 caracteres
}
```

### CommentValue - Value Object Similar

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\ValueObject;

final readonly class CommentValue
{
    private const MIN_LENGTH = 1;
    private const MAX_LENGTH = 1000;

    public function __construct(private string $value)
    {
        $this->validate();
    }

    private function validate(): void
    {
        if (strlen($this->value) < self::MIN_LENGTH) {
            throw new CommentTooShortException();
        }

        if (strlen($this->value) > self::MAX_LENGTH) {
            throw new TextIsLongerThanAllowedException(
                "El comentario no puede exceder " . self::MAX_LENGTH . " caracteres"
            );
        }

        if (trim($this->value) === '') {
            throw new CommentCannotBeEmptyException();
        }
    }

    public function value(): string
    {
        return $this->value;
    }
}
```

### ImageValue - Validación de URLs

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\ValueObject;

final readonly class ImageValue
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public function __construct(private string $value)
    {
        $this->validate();
    }

    private function validate(): void
    {
        // Validar que sea una URL válida
        if (!filter_var($this->value, FILTER_VALIDATE_URL)) {
            throw new InvalidImageUrlException(
                "La URL de imagen no es válida: " . $this->value
            );
        }

        // Validar extensión
        $extension = strtolower(pathinfo(parse_url($this->value, PHP_URL_PATH), PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            throw new InvalidImageExtensionException(
                "La extensión de imagen no es permitida: " . $extension
            );
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function filename(): string
    {
        return basename(parse_url($this->value, PHP_URL_PATH));
    }
}
```

### Beneficios de Value Objects

1. **Encapsulación de validación**: La validación ocurre en el constructor
2. **Reusabilidad**: Se usa en múltiples contextos (Post, Comment, etc.)
3. **Type Safety**: El IDE sabe que es un `BodyValue`, no un `string`
4. **Documentación viva**: El código expresa las restricciones

---

## Domain Events

Los eventos de dominio representan cosas importantes que pasaron en el aggregado.

### PostCreatedDomainEvent

**Ubicación:** `src/Contexts/Web/Post/Domain/Events/PostCreatedDomainEvent.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Events;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class PostCreatedDomainEvent implements DomainEvent
{
    public function __construct(
        public Uuid $id,
        public array $resources
    )
    {
    }

    public static function eventName(): string
    {
        return 'post.created';
    }

    public function aggregateId(): Uuid
    {
        return $this->id;
    }
}
```

**Uso en la entidad:**
```php
// En Post::create() factory method
$post = new self($id, $body, $user, $sharedPostId);

// Registrar evento
$post->record(new PostCreatedDomainEvent($id, $resources));

return $post;
```

**Subscriber para procesar el evento:**

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Web\Post\Domain\Events\PostCreatedDomainEvent;

final readonly class PostResourceUploaderSubscriber implements DomainEventSubscriber
{
    public static function subscribedTo(): string
    {
        return PostCreatedDomainEvent::eventName();
    }

    public function __invoke(PostCreatedDomainEvent $event): void
    {
        // Procesar la subida de recursos asociados al post
        foreach ($event->resources as $resource) {
            // Subir imagen, guardar en BD, etc.
        }
    }
}
```

### PostLikedDomainEvent

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Events;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class PostLikedDomainEvent implements DomainEvent
{
    public function __construct(
        public Uuid $postId,
        public Uuid $userId
    )
    {
    }

    public static function eventName(): string
    {
        return 'post.liked';
    }

    public function aggregateId(): Uuid
    {
        return $this->postId;
    }
}
```

**Uso:**
```php
// En PostLiker::__invoke()
$post->recordThat(new PostLikedDomainEvent($postId, $userId));
$this->repository->save($post);
```

### PostCommentedDomainEvent

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Events;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class PostCommentedDomainEvent implements DomainEvent
{
    public function __construct(
        public Uuid $postId,
        public Uuid $commentId,
        public Uuid $userId
    )
    {
    }

    public static function eventName(): string
    {
        return 'post.commented';
    }

    public function aggregateId(): Uuid
    {
        return $this->postId;
    }
}
```

### Beneficios de Domain Events

1. **Comunicación entre agregados**: Los eventos alertan a otros bounded contexts
2. **Auditoría**: Se registra qué pasó y cuándo
3. **Desacoplamiento**: El post no necesita saber quién lo procesa
4. **Reactividad**: Se pueden ejecutar acciones asincrónicas

---

## Repositories y Búsqueda

Los repositorios abstraen la persistencia y permiten búsquedas complejas.

### Interfaz: PostRepository

**Ubicación:** `src/Contexts/Web/Post/Domain/PostRepository.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use Exception;

interface PostRepository
{
    /**
     * Guardar o actualizar un post
     */
    public function save(Post $post): void;

    /**
     * Obtener un post por ID
     */
    public function findById(Uuid $id): Post;

    /**
     * Obtener todos los posts (con límite)
     */
    public function findAll(int $limit = 10, int $offset = 0): array;

    /**
     * Buscar posts con criterios
     * 
     * Criterios soportados:
     * - 'userId': Filtrar por usuario propietario
     * - 'createdFrom': Filtrar desde fecha
     * - 'createdTo': Filtrar hasta fecha
     * - 'limit': Cantidad de resultados
     * - 'offset': Inicio de paginación
     */
    public function searchByCriteria(array $criteria): array;

    /**
     * Obtener todos sin paginación
     */
    public function searchAll(): array;

    /**
     * Contar posts con criterios
     */
    public function countByCriteria(array $criteria): int;

    /**
     * Buscar por usuario
     */
    public function findByUserId(Uuid $userId): array;

    /**
     * Encontrar cantidad de shares de un post
     */
    public function findSharesQuantity(Uuid $postId): int;

    /**
     * Eliminar un post
     */
    public function delete(Uuid $id): void;
}
```

### Implementación: MysqlPostRepository

**Ubicación:** `src/Contexts/Web/Post/Infrastructure/Persistence/MysqlPostRepository.php`

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Post;
use App\Contexts\Web\Post\Domain\PostRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MysqlPostRepository extends ServiceEntityRepository implements PostRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct($registry, Post::class);
    }

    public function save(Post $post): void
    {
        $this->getEntityManager()->persist($post);
        $this->getEntityManager()->flush();
    }

    /**
     * @throws Exception
     */
    public function findById(Uuid $id): Post
    {
        $post = $this->find($id);

        if ($post === null) {
            throw new PostNotFoundException(
                "Post con ID " . $id->value() . " no encontrado"
            );
        }

        return $post;
    }

    public function findAll(int $limit = 10, int $offset = 0): array
    {
        return $this->findBy([], ['createdAt' => 'DESC'], $limit, $offset);
    }

    public function searchByCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder('p');

        // Filtro por usuario
        if (isset($criteria['userId'])) {
            $qb->andWhere('p.user = :userId')
                ->setParameter('userId', new Uuid($criteria['userId']));
        }

        // Filtro por fecha de creación
        if (isset($criteria['createdFrom'])) {
            $qb->andWhere('p.createdAt >= :createdFrom')
                ->setParameter('createdFrom', new \DateTime($criteria['createdFrom']));
        }

        if (isset($criteria['createdTo'])) {
            $qb->andWhere('p.createdAt <= :createdTo')
                ->setParameter('createdTo', new \DateTime($criteria['createdTo']));
        }

        // Paginación
        $limit = $criteria['limit'] ?? 10;
        $offset = $criteria['offset'] ?? 0;

        $qb->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $qb->getQuery()->getResult();
    }

    public function searchAll(): array
    {
        return $this->findAll(999999, 0);  // Sin límite real
    }

    public function countByCriteria(array $criteria): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)');

        if (isset($criteria['userId'])) {
            $qb->andWhere('p.user = :userId')
                ->setParameter('userId', new Uuid($criteria['userId']));
        }

        return (int)$qb->getQuery()->getSingleScalarResult();
    }

    public function findByUserId(Uuid $userId): array
    {
        return $this->findBy(['user' => $userId], ['createdAt' => 'DESC']);
    }

    public function findSharesQuantity(Uuid $postId): int
    {
        $result = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.sharedPostId = :postId')
            ->setParameter('postId', $postId)
            ->getQuery()
            ->getSingleScalarResult();

        return (int)$result;
    }

    public function delete(Uuid $id): void
    {
        $post = $this->findById($id);
        $this->getEntityManager()->remove($post);
        $this->getEntityManager()->flush();
    }
}
```

### Búsqueda desde la Aplicación

```php
// En PostSearcher::__invoke()
public function __invoke(?array $criteria): PostCollectionResponse
{
    // Merge con valores por defecto
    $criteriaWithDefaults = array_merge(
        ["limit" => 0, "offset" => 0],
        $criteria ?? [],
    );

    // Buscar posts según criterios
    $posts = $criteria
        ? $this->repository->searchByCriteria($criteria)
        : $this->repository->searchAll();

    // Enriquecer posts con información adicional
    foreach ($posts as $post) {
        // Cargar recursos
        $post->setResourceUrls($this->getPostResources->__invoke($post));

        // Cargar URL de imagen de perfil
        if (!empty($post->getUser()->getProfileImage()->value())) {
            $post->getUser()->setUrlProfileImage(
                $this->fileManager->generateTemporaryUrl(
                    "user/profile",
                    $post->getUser()->getProfileImage()->value(),
                ),
            );
        }

        // Cargar post compartido si existe
        $sharedPost = null;
        if ($post->getSharedPostId()) {
            $sharedPost = $this->repository->findById($post->getSharedPostId());
            $sharedPost->setResourceUrls($this->getPostResources->__invoke($sharedPost));
            $post->setSharedPost($sharedPost);
        }

        // Contar shares
        $sharesQuantity = $this->repository->findSharesQuantity($post->getId());
        $post->setSharesQuantity($sharesQuantity);
    }

    // Contar total para paginación
    $total = $this->repository->countByCriteria($criteriaWithDefaults);

    return new PostCollectionResponse(
        $posts,
        $criteriaWithDefaults,
        $total,
    );
}
```

---

## Use Cases Complejos

### Crear Post con Recursos

Este es un use case más complejo que involucra:
1. Validación de datos
2. Creación de agregado
3. Procesamiento de recursos
4. Publicación de eventos

**Command:**
```php
final readonly class CreatePostCommand implements Command
{
    public function __construct(
        public string $id,
        public string $body,
        public array $resources,      // IDs de recursos temporales
        public ?string $sharedPostId,
        public string $userId,
    ) {}
}
```

**Handler:**
```php
final readonly class CreatePostCommandHandler implements CommandHandler
{
    public function __construct(
        private PostCreator $creator,
        private UserRepository $userRepository,
    ) {}

    public function __invoke(CreatePostCommand $command): void
    {
        // 1. Validar que el usuario existe
        $user = $this->userRepository->findById(new Uuid($command->userId));

        // 2. Crear los Value Objects
        $id = new Uuid($command->id);
        $body = new BodyValue($command->body);

        // 3. Validar post compartido si existe
        $sharedPostId = null;
        if ($command->sharedPostId) {
            $sharedPostId = new Uuid($command->sharedPostId);
            // Se valida en el repositorio dentro de Creator
        }

        // 4. Crear el post y sus recursos
        $this->creator->__invoke(
            $id,
            $body,
            $user,
            $command->resources,
            $sharedPostId
        );
        
        // Aquí se registran eventos que se publican automáticamente
    }
}
```

**Use Case (PostCreator):**
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
        // 1. Crear el agregado
        $post = Post::create($id, $body, $user, $resourceIds, $sharedPostId);

        // 2. Procesar recursos
        foreach ($resourceIds as $resourceId) {
            $resource = $this->resourceRepository->findById(new Uuid($resourceId));
            $post->addResource($resource);
        }

        // 3. Persistir
        $this->repository->save($post);
        
        // 4. Los eventos registrados en Post se publican automáticamente
    }
}
```

### Dar Like a un Post

Caso más simple pero con protecciones:

**Command:**
```php
final readonly class LikePostCommand implements Command
{
    public function __construct(
        public string $postId,
        public string $userId
    ) {}
}
```

**Handler:**
```php
final readonly class LikePostCommandHandler implements CommandHandler
{
    public function __construct(
        private PostLiker $liker,
    ) {}

    public function __invoke(LikePostCommand $command): void
    {
        $postId = new Uuid($command->postId);
        $userId = new Uuid($command->userId);
        $this->liker->__invoke($postId, $userId);
    }
}
```

**Use Case (PostLiker):**
```php
final readonly class PostLiker
{
    public function __construct(
        private PostRepository $postRepository,
        private LikeRepository $likeRepository,
    ) {}

    public function __invoke(Uuid $postId, Uuid $userId): void
    {
        // 1. Obtener el post
        $post = $this->postRepository->findById($postId);

        // 2. Verificar que el usuario no haya dado like ya
        $existingLike = $this->likeRepository->findByPostAndUser($postId, $userId);
        if ($existingLike) {
            throw new PostAlreadyLikedException(
                "Ya has dado like a este post"
            );
        }

        // 3. Crear el like
        $like = Like::create($postId, $userId);

        // 4. Agregar al post
        $post->addLike($like);

        // 5. Registrar evento
        $post->recordThat(new PostLikedDomainEvent($postId, $userId));

        // 6. Persistir
        $this->postRepository->save($post);
        $this->likeRepository->save($like);
    }
}
```

---

## Validación Multinivel

La validación ocurre en diferentes niveles:

### Nivel 1: Simetría HTTP (Symfony Request)

```php
// En AddPostCommentRequest
final readonly class AddPostCommentRequest extends BaseRequest
{
    #[Assert\NotNull]
    #[Assert\Type("string")]
    public mixed $commentId;
    
    #[Assert\NotNull]
    #[Assert\Type("string")]
    public mixed $commentBody;
}

// El middleware de Symfony valida antes de llamar al controller
```

### Nivel 2: Lógica DTO (ApiController)

```php
// En CreatePostController
$input = CreatePostRequest::fromHttp($request, $id, $sessionId);
$this->validateRequest($input);  // Valida aquí

$command = $input->toCommand();
```

### Nivel 3: Dominio (Value Objects)

```php
// En CreatePostCommandHandler
$body = new BodyValue($command->body);  // Lanza excepción si inválido

// Value Objects validan en constructor
private function validate(): void
{
    if (strlen($this->value) < self::MIN_LENGTH) {
        throw new BodyTooShortException(...);
    }
    // ...
}
```

### Nivel 4: Use Case (Business Logic)

```php
// En PostLiker
$existingLike = $this->likeRepository->findByPostAndUser($postId, $userId);
if ($existingLike) {
    throw new PostAlreadyLikedException(...);  // Regla de negocio
}
```

### Flujo de Validación

```
HTTP Request
    ↓
Symfony Validation (atributos) ← FALLBACK: ValidationException
    ↓
DTO Validation ← FALLBACK: ValidationException
    ↓
Value Object Creation ← FALLBACK: DomainException
    ↓
Business Logic Validation ← FALLBACK: DomainException
    ↓
Persistencia
```

---

## Handling de Recursos

Los recursos (imágenes, archivos) se manejan de forma especial:

### Modelo de Datos

```php
// En Post aggregate
#[
    ORM\OneToMany(
        targetEntity: PostResource::class,
        mappedBy: "post",
        cascade: ["persist", "remove"],
    ),
]
private ?Collection $resources;

// PostResource es una entidad separada
class PostResource
{
    #[ORM\Id]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Post::class)]
    private Post $post;

    #[ORM\Column(type: "string")]
    private string $filename;

    #[ORM\Column(type: "string")]
    private string $originalName;

    // ...
}
```

### Workflow de Upload de Recursos

```
1. Cliente crea POST temporal: POST /api/post/{id}/resource
   ├─ Sube archivo
   ├─ Se guarda en storage temporal
   └─ Retorna ID del recurso

2. Cliente crea Post: PUT /api/post/{id}
   ├─ Incluye resourceIds en payload
   ├─ CreatePostCommandHandler recibe los IDs
   └─ PostCreator obtiene recursos temporales y los asocia

3. Evento PublishEvent
   ├─ PostResourceUploaderSubscriber escucha PostCreatedDomainEvent
   ├─ Mueve recursos de temporal a permanente
   └─ Actualiza referencias en BD
```

**Controller de Upload Temporal:**

```php
final class AddPostTemporaryResourceController extends ApiController
{
    public function __invoke(
        Request $request,
        string $id,
        string $sessionId
    ): Response {
        // Subir a almacenamiento temporal
        $file = $request->files->get('file');
        
        $tempResourceId = $this->uploadService->uploadTemporary(
            $file,
            $sessionId
        );

        // Retornar ID para usar en la creación del post
        return $this->successResponse([
            'resourceId' => $tempResourceId
        ]);
    }
}
```

---

## Excepciones de Dominio

Las excepciones representan errores de lógica de negocio.

### Interfaz Base

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Exception;

use DomainException;

abstract class PostDomainException extends DomainException {}
```

### Excepciones Específicas

**PostNotFoundException:**
```php
final class PostNotFoundException extends PostDomainException
{
    // Cuando no existe un post con el ID dado
}
```

**PostAlreadyExistsException:**
```php
final class PostAlreadyExistsException extends PostDomainException
{
    // Cuando se intenta crear un post que ya existe
}
```

**PostDeletionNotAllowedException:**
```php
final class PostDeletionNotAllowedException extends PostDomainException
{
    // Cuando un usuario intenta eliminar un post que no es suyo
}
```

**CommentNotFoundException:**
```php
final class CommentNotFoundException extends PostDomainException
{
    // Cuando no existe un comentario con el ID dado
}
```

### Mapeo a HTTP Status

```php
// En ApiExceptionsHttpStatusCodeMapping
const MAPPING = [
    PostNotFoundException::class => 404,
    PostAlreadyExistsException::class => 409,
    PostDeletionNotAllowedException::class => 403,
    CommentNotFoundException::class => 404,
];
```

### ExceptionListener

```php
final class ExceptionListener implements EventListenerInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        
        // Obtener código HTTP del mapeo
        $statusCode = ApiExceptionsHttpStatusCodeMapping::map(
            get_class($exception)
        );

        // Retornar respuesta JSON
        $response = new JsonResponse(
            ['error' => $exception->getMessage()],
            $statusCode
        );

        $event->setResponse($response);
    }
}
```

---

## Testabilidad

La arquitectura DDD/CQRS permite testing en múltiples niveles:

### Test de Value Objects

```php
use PHPUnit\Framework\TestCase;

class BodyValueTest extends TestCase
{
    public function testConstructionWithValidBody(): void
    {
        $body = new BodyValue("Mi post válido");
        
        $this->assertEquals("Mi post válido", $body->value());
        $this->assertEquals(14, $body->length());
    }

    public function testConstructionWithEmptyBodyThrows(): void
    {
        $this->expectException(BodyCannotBeEmptyException::class);
        
        new BodyValue("");
    }

    public function testConstructionWithTooLongBodyThrows(): void
    {
        $this->expectException(TextIsLongerThanAllowedException::class);
        
        new BodyValue(str_repeat("a", 5001));
    }

    public function testEquals(): void
    {
        $body1 = new BodyValue("Mi post");
        $body2 = new BodyValue("Mi post");
        $body3 = new BodyValue("Otro post");

        $this->assertTrue($body1->equals($body2));
        $this->assertFalse($body1->equals($body3));
    }
}
```

### Test de Command Handlers (Unit)

```php
use PHPUnit\Framework\TestCase;

class CreatePostCommandHandlerTest extends TestCase
{
    private CreatePostCommandHandler $handler;
    private PostRepository $postRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        // Mock de dependencias
        $this->postRepository = $this->createMock(PostRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);

        $creator = new PostCreator($this->postRepository);
        
        $this->handler = new CreatePostCommandHandler(
            $creator,
            $this->userRepository
        );
    }

    public function testHandleCreatePostCommand(): void
    {
        // Setup
        $userId = new Uuid('550e8400-e29b-41d4-a716-446655440000');
        $user = new User($userId, ...);
        
        $this->userRepository
            ->method('findById')
            ->willReturn($user);

        $this->postRepository
            ->expects($this->once())
            ->method('save');

        // Ejecutar
        $command = new CreatePostCommand(
            '550e8400-e29b-41d4-a716-446655440001',
            'Mi post',
            [],
            null,
            $userId->value()
        );

        $this->handler->__invoke($command);

        // El mock verificó que save() fue llamado
    }
}
```

### Test de Queries (Integration)

```php
use ApiTestCase;

class FindPostQueryTest extends ApiTestCase
{
    public function testFindPostReturnsCorrectResponse(): void
    {
        // Setup: crear un post en la base de datos
        $post = $this->fixtures->createPost('Mi post');

        // Ejecutar Query
        $query = new FindPostQuery($post->getId()->value());
        $response = $this->queryBus->ask($query);

        // Verificaciones
        $this->assertInstanceOf(PostResponse::class, $response);
        $this->assertEquals('Mi post', $response->body);
        $this->assertEquals($post->getUser()->getUsername()->value(), $response->username);
    }
}
```

### Test de Controllers (Integration)

```php
use ApiTestCase;

class CreatePostControllerTest extends ApiTestCase
{
    public function testCreatePostWithValidData(): void
    {
        // Setup
        $user = $this->fixtures->createUser();
        $token = $this->generateJWT($user->getId()->value());

        // Request HTTP
        $response = $this->client->request('PUT', '/api/post/new-id', [
            'headers' => [
                'Authorization' => $token,
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'body' => 'Mi primer post',
                'resources' => [],
                'sharedPostId' => null
            ]
        ]);

        // Verificaciones
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(
            $this->fixtures->postExists('new-id')
        );
    }

    public function testCreatePostWithoutAuthenticationThrows(): void
    {
        // Request sin token
        $response = $this->client->request('PUT', '/api/post/new-id', [
            'json' => ['body' => 'Mi post']
        ]);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testCreatePostWithInvalidBodyThrows(): void
    {
        $user = $this->fixtures->createUser();
        $token = $this->generateJWT($user->getId()->value());

        $response = $this->client->request('PUT', '/api/post/new-id', [
            'headers' => ['Authorization' => $token],
            'json' => ['body' => '']  // Body vacío
        ]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertStringContainsString('NotBlank', (string)$response->getContent());
    }
}
```

---

## Conclusión: Principios Clave

1. **Value Objects**: Encapsulan validación y lógica
2. **Domain Events**: Comunican cambios importantes
3. **Repositories**: Abstraen persistencia
4. **Use Cases**: Orquestan la lógica de negocio
5. **Validación Multinivel**: Garantiza integridad en todos los niveles
6. **Excepciones de Dominio**: Comunican errores de negocio
7. **Testabilidad**: La arquitectura permite tests en todos los niveles

Esta arquitectura garantiza código mantenible, escalable y testeble.
