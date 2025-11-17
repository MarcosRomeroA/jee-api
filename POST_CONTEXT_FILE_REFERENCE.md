# Referencia de Archivos: Bounded Context Post

Documento de referencia rápida de todos los archivos principales del bounded context de Post.

## Tabla de Directorios

```
src/
├── Apps/Web/Post/                              # Capa de Aplicación (API)
│   ├── AddPostComment/
│   │   ├── AddPostCommentController.php        # Controller
│   │   └── AddPostCommentRequest.php           # DTO request
│   ├── AddPostTempResource/
│   │   └── AddPostTemporaryResourceController.php
│   ├── Create/
│   │   ├── CreatePostController.php            # Controller
│   │   └── CreatePostRequest.php               # DTO request
│   ├── Delete/
│   │   └── DeletePostController.php            # Controller
│   ├── Dislike/
│   │   └── DislikePostController.php           # Controller
│   ├── Find/
│   │   └── FindPostController.php              # Controller
│   ├── FindBatch/
│   │   └── FindPostsByIdsController.php        # Controller
│   ├── Like/
│   │   └── LikePostController.php              # Controller
│   ├── Search/
│   │   ├── SearchPostsController.php           # Controller
│   │   └── SearchPostsRequest.php              # DTO request
│   ├── SearchMyFeed/
│   │   ├── SearchMyFeedController.php          # Controller
│   │   └── SearchMyFeedRequest.php             # DTO request
│   ├── SearchPostComments/
│   │   ├── SearchPostCommentsController.php    # Controller
│   │   └── SearchPostCommentsRequest.php       # DTO request
│   ├── SearchPostLikes/
│   │   ├── SearchPostLikesController.php       # Controller
│   │   └── SearchPostLikesRequest.php          # DTO request
│   └── SearchPostShares/
│       ├── SearchPostSharesController.php      # Controller
│       └── SearchPostSharesRequest.php         # DTO request
│
└── Contexts/Web/Post/                          # Bounded Context
    ├── Application/                            # Lógica de Aplicación
    │   ├── AddComment/
    │   │   ├── AddCommentPostCommand.php       # Command
    │   │   ├── AddCommentPostCommandHandler.php # Command Handler
    │   │   └── PostCommenter.php               # Use Case
    │   ├── Create/
    │   │   ├── CreatePostCommand.php           # Command
    │   │   ├── CreatePostCommandHandler.php    # Command Handler
    │   │   ├── PostCreator.php                 # Use Case
    │   │   └── PostResourceUploaderSubscriber.php # Event Subscriber
    │   ├── Delete/
    │   │   ├── DeletePostCommand.php           # Command
    │   │   ├── DeletePostCommandHandler.php    # Command Handler
    │   │   └── PostDeleter.php                 # Use Case
    │   ├── Dislike/
    │   │   ├── DislikePostCommand.php          # Command
    │   │   ├── DislikePostCommandHandler.php   # Command Handler
    │   │   └── PostDisliker.php                # Use Case
    │   ├── Find/
    │   │   ├── FindPostQuery.php               # Query
    │   │   ├── FindPostQueryHandler.php        # Query Handler
    │   │   └── PostFinder.py                   # Use Case
    │   ├── FindBatch/
    │   │   ├── FindPostsByIdsQuery.php         # Query
    │   │   ├── FindPostsByIdsQueryHandler.php  # Query Handler
    │   │   └── PostsBatchFinder.php            # Use Case
    │   ├── Like/
    │   │   ├── LikePostCommand.php             # Command
    │   │   ├── LikePostCommandHandler.php      # Command Handler
    │   │   └── PostLiker.php                   # Use Case
    │   ├── Search/
    │   │   ├── SearchPostQuery.php             # Query
    │   │   ├── SearchPostQueryHandler.php      # Query Handler
    │   │   └── PostSearcher.php                # Use Case
    │   ├── SearchMyFeed/
    │   │   ├── SearchMyFeedQuery.php           # Query
    │   │   ├── SearchMyFeedQueryHandler.php    # Query Handler
    │   │   └── MyFeedSearcher.php              # Use Case
    │   ├── SearchPostComments/
    │   │   ├── SearchPostCommentsQuery.php     # Query
    │   │   ├── SearchPostCommentsQueryHandler.php # Query Handler
    │   │   └── PostCommentSearcher.php         # Use Case
    │   ├── SearchPostLikes/
    │   │   ├── SearchPostLikesQuery.php        # Query
    │   │   ├── SearchPostLikesQueryHandler.php # Query Handler
    │   │   └── PostLikesSearcher.php           # Use Case
    │   ├── SearchPostShares/
    │   │   ├── SearchPostSharesQuery.php       # Query
    │   │   ├── SearchPostSharesQueryHandler.php # Query Handler
    │   │   └── PostSharesSearcher.php          # Use Case
    │   └── Shared/
    │       ├── GetPostResources.php            # Service compartido
    │       ├── LikeCollectionResponse.php      # Response DTO
    │       ├── LikeResponse.php                # Response DTO
    │       ├── PostCollectionResponse.php      # Response DTO
    │       ├── PostCommentCollectionResponse.php # Response DTO
    │       ├── PostCommentResponse.php         # Response DTO
    │       ├── PostResponse.php                # Response DTO
    │       ├── ShareCollectionResponse.php     # Response DTO
    │       └── ShareResponse.php               # Response DTO
    │
    ├── Domain/                                 # Lógica de Dominio
    │   ├── Comment.php                         # Entidad
    │   ├── CommentRepository.php               # Interfaz repositorio
    │   ├── Like.php                            # Entidad
    │   ├── LikeRepository.php                  # Interfaz repositorio
    │   ├── Post.php                            # Agregado raíz
    │   ├── PostRepository.php                  # Interfaz repositorio
    │   ├── PostResource.php                    # Entidad
    │   ├── PostResourceRepository.php          # Interfaz repositorio
    │   ├── Tag.php                             # Entidad
    │   ├── Events/
    │   │   ├── PostCommentedDomainEvent.php   # Domain Event
    │   │   ├── PostCreatedDomainEvent.php     # Domain Event
    │   │   └── PostLikedDomainEvent.php       # Domain Event
    │   ├── Exception/
    │   │   ├── CommentNotFoundException.php    # Domain Exception
    │   │   ├── PostAlreadyExistsException.php # Domain Exception
    │   │   ├── PostDeletionNotAllowedException.php # Domain Exception
    │   │   └── PostNotFoundException.php       # Domain Exception
    │   └── ValueObject/
    │       ├── BodyValue.php                   # Value Object
    │       ├── CommentValue.php                # Value Object
    │       └── ImageValue.php                  # Value Object
    │
    └── Infrastructure/                         # Implementaciones Técnicas
        └── Persistence/
            ├── MysqlCommentRepository.php     # Implementación repository
            ├── MysqlLikeRepository.php        # Implementación repository
            ├── MysqlPostRepository.php        # Implementación repository
            └── MysqlPostResourceRepository.php # Implementación repository

config/
├── routes/
│   ├── routes.yaml                            # Configuración principal
│   └── web/
│       └── post.yaml                          # Rutas de Post
└── services.yaml                              # Inyección de dependencias
```

---

## Archivos Principales por Tipo

### Controllers (Capa de Aplicación)

| Archivo | Método HTTP | Autenticación | Tipo |
|---------|-------------|---------------|------|
| `AddPostCommentController.php` | PUT | Sí | Command |
| `AddPostTemporaryResourceController.php` | POST | Sí | Command |
| `CreatePostController.php` | PUT | Sí | Command |
| `DeletePostController.php` | DELETE | Sí | Command |
| `DislikePostController.php` | PUT | Sí | Command |
| `FindPostController.php` | GET | Sí | Query |
| `FindPostsByIdsController.php` | GET | Sí | Query |
| `LikePostController.php` | PUT | Sí | Command |
| `SearchPostsController.php` | GET | Sí | Query |
| `SearchMyFeedController.php` | GET | Sí | Query |
| `SearchPostCommentsController.php` | GET | Sí | Query |
| `SearchPostLikesController.php` | GET | Sí | Query |
| `SearchPostSharesController.php` | GET | Sí | Query |

### Request DTOs

| Archivo | Controller Asociado | Validación |
|---------|-------------------|-----------|
| `CreatePostRequest.php` | CreatePostController | Assert\NotBlank (body) |
| `AddPostCommentRequest.php` | AddPostCommentController | Assert\NotNull (commentId, commentBody) |
| `SearchPostsRequest.php` | SearchPostsController | Assert\Type("array") (q) |
| `SearchMyFeedRequest.php` | SearchMyFeedController | Assert\Type("array") (q) |
| `SearchPostCommentsRequest.php` | SearchPostCommentsController | Assert\Type("array") (q) |
| `SearchPostLikesRequest.php` | SearchPostLikesController | Assert\Type("array") (q) |
| `SearchPostSharesRequest.php` | SearchPostSharesController | Assert\Type("array") (q) |

### Commands & Handlers

| Command | Handler | Use Case |
|---------|---------|----------|
| `CreatePostCommand` | `CreatePostCommandHandler` | `PostCreator` |
| `AddCommentPostCommand` | `AddCommentPostCommandHandler` | `PostCommenter` |
| `LikePostCommand` | `LikePostCommandHandler` | `PostLiker` |
| `DislikePostCommand` | `DislikePostCommandHandler` | `PostDisliker` |
| `DeletePostCommand` | `DeletePostCommandHandler` | `PostDeleter` |

### Queries & Handlers

| Query | Handler | Use Case |
|-------|---------|----------|
| `FindPostQuery` | `FindPostQueryHandler` | `PostFinder` |
| `SearchPostQuery` | `SearchPostQueryHandler` | `PostSearcher` |
| `SearchMyFeedQuery` | `SearchMyFeedQueryHandler` | `MyFeedSearcher` |
| `FindPostsByIdsQuery` | `FindPostsByIdsQueryHandler` | `PostsBatchFinder` |
| `SearchPostCommentsQuery` | `SearchPostCommentsQueryHandler` | `PostCommentSearcher` |
| `SearchPostLikesQuery` | `SearchPostLikesQueryHandler` | `PostLikesSearcher` |
| `SearchPostSharesQuery` | `SearchPostSharesQueryHandler` | `PostSharesSearcher` |

### Response DTOs

| Archivo | Caso de Uso | Método toArray() |
|---------|-----------|-----------------|
| `PostResponse` | Un post individual | `array<string, mixed>` |
| `PostCollectionResponse` | Colección paginada | `['data' => [], 'metadata' => [...]]` |
| `PostCommentResponse` | Un comentario | `array<string, mixed>` |
| `PostCommentCollectionResponse` | Colección de comentarios | `['data' => [], 'metadata' => [...]]` |
| `LikeResponse` | Un like | `array<string, mixed>` |
| `LikeCollectionResponse` | Colección de likes | `['data' => [], 'metadata' => [...]]` |
| `ShareResponse` | Un share | `array<string, mixed>` |
| `ShareCollectionResponse` | Colección de shares | `['data' => [], 'metadata' => [...]]` |

### Entidades de Dominio

| Archivo | Descripción | Relaciones |
|---------|-----------|-----------|
| `Post` | Agregado raíz | Tiene: Comments, Likes, Resources |
| `Comment` | Comentario en un post | Pertenece a: Post, User |
| `Like` | Like en un post | Pertenece a: Post, User |
| `PostResource` | Recurso asociado | Pertenece a: Post |
| `Tag` | Tag/etiqueta | Pertenece a: Post |

### Value Objects

| Archivo | Validación | Constantes |
|---------|-----------|-----------|
| `BodyValue` | MIN_LENGTH=1, MAX_LENGTH=5000 | No puede estar vacío |
| `CommentValue` | MIN_LENGTH=1, MAX_LENGTH=1000 | No puede estar vacío |
| `ImageValue` | URL válida, extensiones permitidas | ['jpg', 'jpeg', 'png', 'gif', 'webp'] |

### Domain Events

| Evento | Disparado por | Datos | Uso |
|--------|--------------|-------|-----|
| `PostCreatedDomainEvent` | `PostCreator` | postId, resources | Procesar upload de recursos |
| `PostLikedDomainEvent` | `PostLiker` | postId, userId | Notificar al propietario |
| `PostCommentedDomainEvent` | `PostCommenter` | postId, commentId, userId | Notificar al propietario |

### Domain Exceptions

| Excepción | Lanzada por | HTTP Status |
|-----------|------------|-----------|
| `PostNotFoundException` | Repository | 404 |
| `PostAlreadyExistsException` | Use Case | 409 |
| `PostDeletionNotAllowedException` | Use Case | 403 |
| `CommentNotFoundException` | Repository | 404 |

### Repositories (Interfaces)

| Interfaz | Métodos Principales | Implementación |
|----------|-------------------|-----------------|
| `PostRepository` | findById, save, searchByCriteria, delete | `MysqlPostRepository` |
| `CommentRepository` | findById, save, findByPost | `MysqlCommentRepository` |
| `LikeRepository` | findById, save, findByPostAndUser | `MysqlLikeRepository` |
| `PostResourceRepository` | findById, save | `MysqlPostResourceRepository` |

---

## Configuración Clave

### Rutas (config/routes/web/post.yaml)

```yaml
# Crear post
create_post:
    path: /post/{id}
    methods: [PUT]
    defaults: { auth: true }

# Obtener post
find_post:
    path: /post/{id}
    methods: [GET]
    defaults: { auth: true }

# Buscar posts
search_posts:
    path: /posts
    methods: [GET]
    defaults: { auth: true }

# Mi feed
my_feed:
    path: /my-feed
    methods: [GET]
    defaults: { auth: true }

# Like
like:
    path: /post/{id}/like
    methods: [PUT]
    defaults: { auth: true }

# Dislike
dislike:
    path: /post/{id}/dislike
    methods: [PUT]
    defaults: { auth: true }

# Comentario
add_post_comment:
    path: /post/{id}/comment
    methods: [PUT]
    defaults: { auth: true }

# Eliminar
delete:
    path: /post/{id}/delete
    methods: [DELETE]
    defaults: { auth: true }

# Comentarios
search_post_comments:
    path: /post/{id}/comments
    methods: [GET]
    defaults: { auth: true }

# Likes
search_post_likes:
    path: /post/{id}/likes
    methods: [GET]
    defaults: { auth: true }

# Shares
search_post_shares:
    path: /post/{id}/shares
    methods: [GET]
    defaults: { auth: true }
```

### Middleware JWT (config/services.yaml)

```yaml
App\Contexts\Shared\Infrastructure\Symfony\JwtAuthMiddleware:
    tags:
        - {
              name: kernel.event_listener,
              event: kernel.request,
              method: onKernelRequest,
          }
```

### Command/Query Bus (config/services.yaml)

```yaml
App\Contexts\Shared\Infrastructure\CQRS\Command\InMemorySymfonyCommandBus:
    arguments: [!tagged app.command_handler]

App\Contexts\Shared\Infrastructure\CQRS\Query\InMemorySymfonyQueryBus:
    arguments: [!tagged app.query_handler]
```

---

## Patrones de Nombres

### Commands
```
{Acción}PostCommand
```
Ejemplos:
- `CreatePostCommand`
- `LikePostCommand`
- `DeletePostCommand`

### Handlers
```
{Acción}PostCommandHandler
{Acción}PostQueryHandler
```
Ejemplos:
- `CreatePostCommandHandler`
- `FindPostQueryHandler`

### Use Cases (Servicios de Aplicación)
```
Post{Acción}
```
Ejemplos:
- `PostCreator`
- `PostLiker`
- `PostDeleter`

### Controllers
```
{Acción}PostController
```
Ejemplos:
- `CreatePostController`
- `LikePostController`

### Request DTOs
```
{Acción}PostRequest
```
Ejemplos:
- `CreatePostRequest`
- `SearchPostsRequest`

### Responses
```
{Entidad}Response
{Entidad}CollectionResponse
```
Ejemplos:
- `PostResponse`
- `PostCollectionResponse`
- `LikeResponse`

### Queries
```
{Acción}PostQuery
Search{Entidad}Query
```
Ejemplos:
- `FindPostQuery`
- `SearchPostQuery`
- `SearchPostCommentsQuery`

### Repositories (Interfaz)
```
{Entidad}Repository
```
Ejemplos:
- `PostRepository`
- `CommentRepository`

### Repositories (Implementación)
```
Mysql{Entidad}Repository
```
Ejemplos:
- `MysqlPostRepository`
- `MysqlCommentRepository`

---

## Capas y Responsabilidades

### Capa de Presentación (Apps/Web/Post/)
- **Controllers**: Orquestar request/response
- **Request DTOs**: Validar y transformar datos HTTP
- **Responsabilidad**: Mapear HTTP a Commands/Queries

### Capa de Aplicación (Contexts/Web/Post/Application/)
- **Commands/Queries**: Objeto de transferencia de datos
- **Handlers**: Ejecutar Commands/Queries
- **Use Cases**: Implementar lógica de negocio
- **Response DTOs**: Retornar datos al cliente
- **Responsabilidad**: Orquestar agregados y servicios

### Capa de Dominio (Contexts/Web/Post/Domain/)
- **Agregados** (Post): Encapsular lógica y estado
- **Entidades** (Comment, Like): Identidad única
- **Value Objects** (BodyValue): Validar datos
- **Repositories**: Abstraer persistencia
- **Events**: Comunicar cambios
- **Exceptions**: Errores de negocio
- **Responsabilidad**: Expresar reglas de negocio

### Capa de Infraestructura (Contexts/Web/Post/Infrastructure/)
- **Repository Implementations**: Acceso a BD
- **Responsabilidad**: Detalles técnicos

---

## Flujos de Datos

### Write Flow (Command)

```
HTTP Request (PUT /api/post/{id})
    ↓
JwtAuthMiddleware (extrae sessionId)
    ↓
CreatePostController::__invoke()
    ├─ CreatePostRequest::fromHttp()
    ├─ validateRequest()
    ├─ toCommand() → CreatePostCommand
    └─ $commandBus->dispatch()
    
CommandBus
    └─ CreatePostCommandHandler::__invoke()
        ├─ Convierte a Value Objects
        └─ $creator->__invoke()

PostCreator
    ├─ Crea Post aggregate
    ├─ Registra eventos
    └─ $postRepository->save()

PostRepository
    └─ Persiste en BD

EventBus (automático)
    └─ Publica eventos

Subscribers
    ├─ PostResourceUploaderSubscriber
    └─ Ejecutan lógica reactiva

HTTP Response (200 OK)
```

### Read Flow (Query)

```
HTTP Request (GET /api/post/{id})
    ↓
JwtAuthMiddleware (extrae sessionId)
    ↓
FindPostController::__invoke(id)
    ├─ Crea FindPostQuery
    └─ $queryBus->ask()

QueryBus
    └─ FindPostQueryHandler::__invoke()
        └─ $finder->__invoke()

PostFinder
    ├─ $postRepository->findById()
    ├─ Enriquece con datos adicionales
    └─ PostResponse::fromEntity()

PostResponse
    └─ toArray()

ApiController
    └─ successResponse()

HTTP Response (200 OK + JSON)
```

---

## Checklist de Referencia Rápida

Cuando añadas un nuevo endpoint, verifica:

```
[ ] Controller en: src/Apps/Web/Post/{UseCase}/
[ ] Request DTO en: src/Apps/Web/Post/{UseCase}/ (si aplica)
[ ] Command/Query en: src/Contexts/Web/Post/Application/{UseCase}/
[ ] Handler en: src/Contexts/Web/Post/Application/{UseCase}/
[ ] Use Case en: src/Contexts/Web/Post/Application/{UseCase}/
[ ] Response en: src/Contexts/Web/Post/Application/Shared/
[ ] Domain Event en: src/Contexts/Web/Post/Domain/Events/ (si aplica)
[ ] Exception en: src/Contexts/Web/Post/Domain/Exception/ (si aplica)
[ ] Ruta en: config/routes/web/post.yaml
[ ] Exception mapped en: ApiExceptionsHttpStatusCodeMapping
```

---

## Búsqueda Rápida

### "¿Dónde está el código que crea un post?"
- Controller: `src/Apps/Web/Post/Create/CreatePostController.php`
- Command: `src/Contexts/Web/Post/Application/Create/CreatePostCommand.php`
- Handler: `src/Contexts/Web/Post/Application/Create/CreatePostCommandHandler.php`
- Use Case: `src/Contexts/Web/Post/Application/Create/PostCreator.php`
- Request DTO: `src/Apps/Web/Post/Create/CreatePostRequest.php`

### "¿Dónde se buscan posts?"
- Controller: `src/Apps/Web/Post/Search/SearchPostsController.php`
- Query: `src/Contexts/Web/Post/Application/Search/SearchPostQuery.php`
- Handler: `src/Contexts/Web/Post/Application/Search/SearchPostQueryHandler.php`
- Use Case: `src/Contexts/Web/Post/Application/Search/PostSearcher.php`
- Request DTO: `src/Apps/Web/Post/Search/SearchPostsRequest.php`

### "¿Dónde están los Response DTOs?"
- `src/Contexts/Web/Post/Application/Shared/*Response.php`

### "¿Dónde se define si un endpoint requiere autenticación?"
- `config/routes/web/post.yaml` - línea `defaults: { auth: true }`

### "¿Dónde se valida el JWT?"
- `src/Contexts/Shared/Infrastructure/Symfony/JwtAuthMiddleware.php`

### "¿Dónde se convierten excepciones a HTTP status codes?"
- `src/Contexts/Shared/Infrastructure/Symfony/ApiExceptionsHttpStatusCodeMapping.php`

### "¿Dónde se implementan las reglas de negocio?"
- Use Cases: `src/Contexts/Web/Post/Application/{UseCase}/Post{Action}.php`
- Value Objects: `src/Contexts/Web/Post/Domain/ValueObject/`
- Agregados: `src/Contexts/Web/Post/Domain/Post.php`
