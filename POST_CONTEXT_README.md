# Documentación del Bounded Context: Post

Bienvenido a la documentación completa del bounded context de Post. Este proyecto utiliza arquitectura **DDD (Domain-Driven Design)** con **CQRS (Command Query Responsibility Segregation)**.

## Inicio Rápido

### Documentos por Caso de Uso

```
¿Quiero entender la arquitectura?
└─ Lee: BOUNDED_CONTEXT_POST_GUIDE.md
   └─ Secciones: Arquitectura, Controllers, Commands, Queries, Responses

¿Necesito implementar un nuevo endpoint?
└─ Lee: IMPLEMENTATION_CHECKLIST.md
   └─ Seguir los 10 pasos + plantillas code templates

¿Busco ejemplos de código listos para copiar?
└─ Lee: CODE_TEMPLATES.md
   └─ Plantillas para Command, Handler, Use Case, Controller, etc.

¿Necesito encontrar un archivo específico?
└─ Lee: POST_CONTEXT_FILE_REFERENCE.md
   └─ Árbol de directorios + búsqueda rápida

¿Quiero entender patrones avanzados?
└─ Lee: POST_CONTEXT_ADVANCED_PATTERNS.md
   └─ Value Objects, Domain Events, Repositories, Testing

¿No sé por dónde empezar?
└─ Lee: DOCUMENTATION_SUMMARY.md
   └─ Visión general de todos los documentos
```

## Documentación Disponible

### 1. **BOUNDED_CONTEXT_POST_GUIDE.md** (49 KB)
**El documento principal. Leer primero.**

Contiene:
- Diagrama de arquitectura general
- Explicación del JWT Middleware y autenticación
- Clase base ApiController con ejemplos
- Request DTOs (4 ejemplos prácticos)
- Commands (4 ejemplos: Create, Like, Delete, AddComment)
- Queries (4 ejemplos: Find, Search, SearchMyFeed, SearchPostLikes)
- Handlers (CommandHandler y QueryHandler)
- Response DTOs (PostResponse, PostCollectionResponse, etc.)
- Configuración de rutas (archivo completo post.yaml)
- 2 flujos completos de ejecución paso a paso
- Patrones y buenas prácticas
- Checklist de implementación
- Tabla de referencias rápidas

**Cuándo leerlo:** Al principio, para entender cómo funciona todo junto

**Tiempo:** 30-45 minutos

---

### 2. **POST_CONTEXT_ADVANCED_PATTERNS.md** (30 KB)
**Patrones avanzados e implementación de lógica compleja.**

Contiene:
- Value Objects: BodyValue, CommentValue, ImageValue con validación
- Domain Events: PostCreatedDomainEvent, PostLikedDomainEvent
- Repositories: interfaz, implementación MySQL, búsquedas con criterios
- Use cases complejos: crear post con recursos, dar like
- Validación multinivel (4 niveles en cascada)
- Handling de recursos (workflow completo de upload)
- Excepciones de dominio (mapeo a HTTP status)
- Testabilidad: tests unitarios, integration, controller

**Cuándo leerlo:** Después de BOUNDED_CONTEXT_POST_GUIDE.md

**Tiempo:** 30-40 minutos

---

### 3. **IMPLEMENTATION_CHECKLIST.md** (18 KB)
**Guía paso a paso para implementar nuevos endpoints.**

Contiene:
- Ejemplo completo: Endpoint "Compartir Post"
- 10 pasos detallados con código
- Explicación de cada archivo
- 11 items de checklist final
- Plantillas code templates (copiar-pegar)
- Flujo de ejecución resumido
- Troubleshooting (3 problemas comunes)

**Cuándo leerlo:** Cuando vas a implementar un nuevo endpoint

**Tiempo:** 15-20 minutos

---

### 4. **POST_CONTEXT_FILE_REFERENCE.md** (21 KB)
**Referencia rápida de estructura de archivos.**

Contiene:
- Árbol completo de directorios (post/)
- Tablas de archivos por tipo:
  - Controllers
  - Request DTOs
  - Commands & Handlers
  - Queries & Handlers
  - Response DTOs
  - Entidades de dominio
  - Value Objects
  - Domain Events
  - Exceptions
  - Repositories
- Patrones de nombres (convenciones)
- Capas y responsabilidades
- Flujos de datos (write y read)
- Búsqueda rápida (Q&A)

**Cuándo leerlo:** Para encontrar archivos o ubicaciones

**Tiempo:** 5-10 minutos (referencia rápida)

---

### 5. **CODE_TEMPLATES.md** (22 KB)
**Plantillas de código listas para copiar-pegar.**

Contiene plantillas para:
- Command (clase básica)
- CommandHandler
- Use Case
- Query
- QueryHandler
- Controller para Command
- Controller para Query
- Request DTO Simple
- Request DTO con BaseRequest
- Response Individual
- Response Colección
- Domain Event
- Domain Exception
- Ruta YAML
- Ejemplo completo: Compartir Post (todos los archivos)
- Test Template

**Cuándo leerlo:** Cuando necesitas escribir código nuevo

**Tiempo:** 5-10 minutos (búsqueda de plantilla)

---

### 6. **DOCUMENTATION_SUMMARY.md** (20 KB)
**Índice y visión general de toda la documentación.**

Contiene:
- Resumen de cada documento
- Cómo usar la documentación según caso
- Estructura de ejemplo completo
- Patrones clave
- Antes vs Después (comparación)
- Estructura mental (diagrama)
- Checklist de comprensión
- Glosario rápido
- Próximos pasos

**Cuándo leerlo:** Como visión general o para orientarte

**Tiempo:** 15-20 minutos

---

## Estructura de Directorios

```
src/
├── Apps/Web/Post/                    # ← Controllers y Request DTOs
│   ├── Create/
│   │   ├── CreatePostController.php
│   │   └── CreatePostRequest.php
│   ├── Find/
│   ├── Like/
│   ├── Delete/
│   ├── Search/
│   ├── SearchMyFeed/
│   └── ...
│
└── Contexts/Web/Post/                # ← Bounded Context
    ├── Application/                  # Lógica de aplicación
    │   ├── Create/
    │   │   ├── CreatePostCommand.php
    │   │   ├── CreatePostCommandHandler.php
    │   │   └── PostCreator.php       # Use Case
    │   ├── Find/
    │   │   ├── FindPostQuery.php
    │   │   ├── FindPostQueryHandler.php
    │   │   └── PostFinder.php        # Use Case
    │   ├── Shared/
    │   │   ├── PostResponse.php
    │   │   ├── PostCollectionResponse.php
    │   │   └── ...
    │   └── ...
    │
    ├── Domain/                       # Lógica de dominio
    │   ├── Post.php                  # Agregado raíz
    │   ├── Comment.php               # Entidad
    │   ├── Like.php                  # Entidad
    │   ├── PostRepository.php        # Interfaz
    │   ├── Events/
    │   │   ├── PostCreatedDomainEvent.php
    │   │   └── PostLikedDomainEvent.php
    │   ├── Exception/
    │   │   ├── PostNotFoundException.php
    │   │   └── ...
    │   └── ValueObject/
    │       ├── BodyValue.php
    │       └── CommentValue.php
    │
    └── Infrastructure/               # Implementaciones técnicas
        └── Persistence/
            ├── MysqlPostRepository.php
            └── ...

config/
├── routes/web/post.yaml              # ← Rutas del bounded context
└── services.yaml                     # ← Inyección de dependencias
```

## Flujo de Request

```
HTTP Request (PUT /api/post/{id})
    ↓
JwtAuthMiddleware
├─ Valida JWT del header Authorization
└─ Extrae sessionId y lo inyecta en la request
    ↓
CreatePostController::__invoke(Request $request, string $id, string $sessionId)
├─ Crea CreatePostRequest::fromHttp()
├─ Valida con validateRequest()
├─ Convierte a CreatePostCommand
└─ Dispone: $this->commandBus->dispatch($command)
    ↓
CommandBus busca CreatePostCommandHandler
    ↓
CreatePostCommandHandler::__invoke(CreatePostCommand $command)
├─ Convierte strings a Value Objects (Uuid, BodyValue)
└─ Ejecuta: $this->creator->__invoke(...)
    ↓
PostCreator::__invoke(Uuid $id, BodyValue $body, User $user, ...)
├─ Crea agregado Post::create()
├─ Registra evento: new PostCreatedDomainEvent(...)
└─ Persiste: $this->repository->save($post)
    ↓
MysqlPostRepository::save(Post $post)
└─ INSERT/UPDATE en base de datos
    ↓
EventBus publica eventos automáticamente
    ↓
Subscribers reaccionan al evento
    ↓
HTTP Response: 200 OK
```

## Patrones Clave

### 1. Separación en Capas
```
Presentación ← Controller + Request DTO
       ↓
Aplicación ← Command/Query + Handler + Use Case
       ↓
Dominio ← Agregado + Entity + Value Object
       ↓
Infraestructura ← Repository + Persistencia
```

### 2. CQRS
- **Commands**: Acciones que modifican estado (Create, Update, Delete)
- **Queries**: Acciones que leen datos (Find, Search)

### 3. Autenticación Integrada
```yaml
defaults: { auth: true }  # ← Activa validación JWT automática
```

### 4. Validación Multinivel
1. **HTTP**: Symfony Request validation
2. **DTO**: Atributos de validación
3. **Domain**: Value Objects en constructor
4. **Use Case**: Reglas de negocio

## Checklist: Cómo Empezar

### Si eres nuevo en el proyecto

```
[ ] 1. Leer BOUNDED_CONTEXT_POST_GUIDE.md (Arquitectura)
       └─ Entender qué es un Command, Query, Handler
       └─ Entender cómo se conectan los archivos

[ ] 2. Leer POST_CONTEXT_ADVANCED_PATTERNS.md (Patrones)
       └─ Entender Value Objects
       └─ Entender Domain Events
       └─ Entender validación multinivel

[ ] 3. Estudiar un endpoint completo
       └─ Abrir CreatePostController
       └─ Seguir el flujo: Controller → Command → Handler → Use Case

[ ] 4. Hacer un pequeño cambio
       └─ Modificar validación de BodyValue
       └─ Ejecutar tests
       └─ Verificar que funciona
```

### Si necesitas implementar un nuevo endpoint

```
[ ] 1. Abrir IMPLEMENTATION_CHECKLIST.md
[ ] 2. Abrir CODE_TEMPLATES.md
[ ] 3. Seguir los 10 pasos
[ ] 4. Usar las plantillas para copiar-pegar
[ ] 5. Completar el checklist final
[ ] 6. Ejecutar tests
```

### Si buscos algo específico

```
[ ] 1. Abrir POST_CONTEXT_FILE_REFERENCE.md
[ ] 2. Usar sección "Búsqueda Rápida"
[ ] 3. O buscar en la tabla correspondiente
```

## Ejemplos de Endpoints Existentes

### Write: Crear Post
- **Archivo del controlador:** `src/Apps/Web/Post/Create/CreatePostController.php`
- **Ruta:** `PUT /api/post/{id}`
- **Autenticación:** Sí
- **Documentación:** BOUNDED_CONTEXT_POST_GUIDE.md → Ejemplo 1: Controller para Command

### Read: Obtener Post
- **Archivo del controlador:** `src/Apps/Web/Post/Find/FindPostController.php`
- **Ruta:** `GET /api/post/{id}`
- **Autenticación:** Sí
- **Documentación:** BOUNDED_CONTEXT_POST_GUIDE.md → Ejemplo 2: Controller para Query

### Action: Like Post
- **Archivo del controlador:** `src/Apps/Web/Post/Like/LikePostController.php`
- **Ruta:** `PUT /api/post/{id}/like`
- **Autenticación:** Sí
- **Documentación:** BOUNDED_CONTEXT_POST_GUIDE.md → Ejemplo 3: Controller para Action

### Search: Buscar Posts
- **Archivo del controlador:** `src/Apps/Web/Post/Search/SearchPostsController.php`
- **Ruta:** `GET /api/posts`
- **Autenticación:** Sí
- **Documentación:** BOUNDED_CONTEXT_POST_GUIDE.md → Ejemplo 4: Controller con Búsqueda

## Validación de Implementación

Después de crear un nuevo endpoint:

```bash
# Verificar sintaxis PHP
php -l src/Apps/Web/Post/NewFeature/Controller.php

# Ejecutar tests
./bin/phpunit tests/Feature/Apps/Web/Post/NewFeature/

# Hacer request HTTP
curl -X PUT http://localhost:8000/api/post/id \
  -H "Authorization: eyJ..." \
  -H "Content-Type: application/json" \
  -d '{"body": "test"}'
```

## Convenciones de Nombres

```
Controllers:          {Action}PostController
Commands:             {Action}PostCommand
Queries:              {Action}PostQuery o Search{Entity}Query
Handlers:             {Action}PostCommandHandler / {Action}PostQueryHandler
Use Cases:            {Action}Post
Request DTOs:         {Action}PostRequest
Response DTOs:        {Entity}Response, {Entity}CollectionResponse
Domain Events:        Post{Action}DomainEvent
Exceptions:           {ErrorDescription}Exception
Repositories:         {Entity}Repository (interfaz)
                      Mysql{Entity}Repository (implementación)
```

## Preguntas Frecuentes

### ¿Dónde está el código que crea un post?
1. Controller: `src/Apps/Web/Post/Create/CreatePostController.php`
2. Command: `src/Contexts/Web/Post/Application/Create/CreatePostCommand.php`
3. Handler: `src/Contexts/Web/Post/Application/Create/CreatePostCommandHandler.php`
4. Use Case: `src/Contexts/Web/Post/Application/Create/PostCreator.php`

### ¿Cómo se valida el JWT?
- Archivo: `src/Contexts/Shared/Infrastructure/Symfony/JwtAuthMiddleware.php`
- Activación: En la ruta, agregar `defaults: { auth: true }`

### ¿Dónde se escriben las reglas de negocio?
- Use Cases: `src/Contexts/Web/Post/Application/{UseCase}/`
- Value Objects: `src/Contexts/Web/Post/Domain/ValueObject/`
- Agregados: `src/Contexts/Web/Post/Domain/Post.php`

### ¿Cómo se manejan los errores?
- Domain Exceptions: `src/Contexts/Web/Post/Domain/Exception/`
- Mapeo a HTTP: `src/Contexts/Shared/Infrastructure/Symfony/ApiExceptionsHttpStatusCodeMapping.php`

### ¿Dónde están los tests?
- Tests: `tests/Feature/Apps/Web/Post/`
- Ejemplos: Ver POST_CONTEXT_ADVANCED_PATTERNS.md

## Errores Comunes

### Error: El controller no recibe sessionId
**Solución:** Asegúrate que la ruta tiene `defaults: { auth: true }`

```yaml
my_route:
    path: /post/{id}
    controller: MyController
    methods: [PUT]
    defaults: { auth: true }  # ← NECESARIO
```

### Error: El CommandHandler no se ejecuta
**Solución:** Verifica que:
1. La clase implementa `CommandHandler`
2. Tiene método `__invoke(Command)`
3. El nombre sigue el patrón: `{Command}Handler`

### Error: Las excepciones no se convierten a HTTP response
**Solución:** Registra la excepción en `ApiExceptionsHttpStatusCodeMapping`

## Recursos Adicionales

- **Architecture Pattern:** Domain-Driven Design + CQRS
- **Framework:** Symfony 6+
- **Database:** Doctrine ORM + MySQL
- **Testing:** PHPUnit

## Soporte

Para dudas sobre la documentación:
1. Revisa los ejemplos de código en los archivos fuente
2. Busca en la sección "Búsqueda Rápida" de POST_CONTEXT_FILE_REFERENCE.md
3. Lee el IMPLEMENTATION_CHECKLIST.md para implementación paso a paso

---

## Mapa Mental Rápido

```
┌─ DOCUMENTACIÓN
│
├─ Nuevo en proyecto?
│  └─ Lee: BOUNDED_CONTEXT_POST_GUIDE.md
│
├─ Implementar endpoint?
│  ├─ Lee: IMPLEMENTATION_CHECKLIST.md
│  └─ Usa: CODE_TEMPLATES.md
│
├─ Buscar archivo?
│  └─ Lee: POST_CONTEXT_FILE_REFERENCE.md
│
├─ Patrones avanzados?
│  └─ Lee: POST_CONTEXT_ADVANCED_PATTERNS.md
│
└─ No sé dónde empezar?
   └─ Lee: DOCUMENTATION_SUMMARY.md
```

---

**Documentación generada:** Noviembre 2024
**Proyecto:** JEE API - Tesis
**Bounded Context:** Post
**Patrón:** DDD + CQRS

Versión de documentación: 1.0
