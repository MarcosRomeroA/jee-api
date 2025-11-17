# Documentación Completa: Bounded Context de Post

Este archivo sirve como índice y resumen de toda la documentación del bounded context de Post.

## Documentos Generados

### 1. **BOUNDED_CONTEXT_POST_GUIDE.md** (Documento Principal)
Guía completa y comprensiva del bounded context de Post.

**Contenidos:**
- Arquitectura general (diagrama de flujo)
- Autenticación JWT Middleware (cómo funciona y se configura)
- Controllers (clase base ApiController + 5 ejemplos prácticos)
- Requests/DTOs (clase base BaseRequest + 4 ejemplos)
- Commands (interfaz + 4 ejemplos específicos)
- Queries (interfaz + 4 ejemplos específicos)
- Handlers (CommandHandler y QueryHandler con ejemplos)
- Responses (clase base + 4 ejemplos de response DTOs)
- Configuración de rutas (archivo post.yaml completo)
- Flujo completo de 2 casos prácticos
- Patrones y buenas prácticas
- Checklist de implementación
- Referencias rápidas

**Ideal para:** Entender la arquitectura completa y los patrones generales.

---

### 2. **POST_CONTEXT_ADVANCED_PATTERNS.md** (Patrones Avanzados)
Documento complementario con patrones avanzados y use cases complejos.

**Contenidos:**
- Value Objects (validación, reusabilidad, ejemplos: BodyValue, CommentValue, ImageValue)
- Domain Events (cómo se crean, se registran, se publican)
- Repositories y búsqueda (interfaz, implementación MySQL, criterios de búsqueda)
- Use cases complejos (crear post con recursos, dar like)
- Validación multinivel (4 niveles de validación)
- Handling de recursos (workflow completo de upload)
- Excepciones de dominio (mapeo a HTTP status)
- Testabilidad (tests en múltiples niveles: unit, integration)

**Ideal para:** Entender cómo implementar lógica compleja siguiendo DDD.

---

### 3. **IMPLEMENTATION_CHECKLIST.md** (Guía Paso a Paso)
Checklist detallado para crear un nuevo endpoint.

**Contenidos:**
- Ejemplo práctico: Crear endpoint "Compartir Post"
- 10 pasos detallados con código
- 11 items de checklist final
- Plantillas rápidas para copiar-pegar
- Flujo de ejecución resumido
- Troubleshooting (3 problemas comunes)
- Conclusión con puntos clave

**Ideal para:** Implementar nuevos endpoints rápidamente.

---

### 4. **POST_CONTEXT_FILE_REFERENCE.md** (Referencia de Archivos)
Documento de referencia rápida de estructura y ubicación de archivos.

**Contenidos:**
- Árbol completo de directorios
- Tablas de archivos por tipo (controllers, requests, commands, queries, responses, etc.)
- Patrones de nombres (convenciones)
- Capas y responsabilidades
- Flujos de datos (write y read)
- Checklist de referencia rápida
- Búsqueda rápida (Q&A)

**Ideal para:** Encontrar dónde está un archivo o entender la estructura.

---

## Cómo Usar Esta Documentación

### Si eres nuevo en el proyecto:

1. Comienza con **BOUNDED_CONTEXT_POST_GUIDE.md**
   - Lee la sección "Arquitectura General"
   - Estudia los 2 flujos completos al final
   - Entiende los patrones básicos

2. Luego lee **POST_CONTEXT_ADVANCED_PATTERNS.md**
   - Profundiza en Value Objects y Domain Events
   - Entiende la validación multinivel
   - Aprende sobre testabilidad

### Si necesitas implementar un nuevo endpoint:

1. Abre **IMPLEMENTATION_CHECKLIST.md**
   - Sigue los 10 pasos
   - Usa las plantillas para copiar-pegar
   - Completa el checklist final

2. Usa **POST_CONTEXT_FILE_REFERENCE.md** para:
   - Verificar dónde crear cada archivo
   - Seguir las convenciones de nombres
   - Encontrar ejemplos similares

### Si necesitas encontrar algo específico:

1. Usa **POST_CONTEXT_FILE_REFERENCE.md**
   - Sección "Búsqueda Rápida"
   - Tablas de referencia

2. Busca en **BOUNDED_CONTEXT_POST_GUIDE.md**
   - Tabla de contenidos
   - Secciones de referencias rápidas

---

## Estructura de Ejemplo Completo

Para que entiendas cómo todo se conecta, aquí está el flujo para "Crear un Post":

### Request HTTP
```bash
PUT /api/post/550e8400-e29b-41d4-a716-446655440000 \
  -H "Authorization: eyJ..." \
  -H "Content-Type: application/json" \
  -d '{"body": "Mi primer post", "resources": []}'
```

### Archivos Involucrados

```
1. Ruta registrada
   └─ config/routes/web/post.yaml: create_post

2. Middleware (automático)
   └─ src/Contexts/Shared/Infrastructure/Symfony/JwtAuthMiddleware.php
      ├─ Valida JWT
      └─ Extrae sessionId

3. Request DTO
   └─ src/Apps/Web/Post/Create/CreatePostRequest.php
      ├─ Valida datos HTTP
      └─ Convierte a Command

4. Controller
   └─ src/Apps/Web/Post/Create/CreatePostController.php
      ├─ Recibe parámetros
      ├─ Crea Command
      └─ Dispone en CommandBus

5. Command
   └─ src/Contexts/Web/Post/Application/Create/CreatePostCommand.php
      └─ Transporta datos al handler

6. Command Handler
   └─ src/Contexts/Web/Post/Application/Create/CreatePostCommandHandler.php
      ├─ Convierte a Value Objects
      └─ Delega a use case

7. Use Case
   └─ src/Contexts/Web/Post/Application/Create/PostCreator.php
      ├─ Crea agregado Post
      ├─ Registra eventos
      └─ Persiste en repositorio

8. Domain Layer
   ├─ src/Contexts/Web/Post/Domain/Post.php (agregado)
   ├─ src/Contexts/Web/Post/Domain/ValueObject/BodyValue.php (value object)
   └─ src/Contexts/Web/Post/Domain/Events/PostCreatedDomainEvent.php (event)

9. Repository Implementation
   └─ src/Contexts/Web/Post/Infrastructure/Persistence/MysqlPostRepository.php
      └─ Persiste en BD

10. Response
    └─ HTTP 200 OK (vacía)
```

---

## Patrones Clave

### 1. Separación en Capas

```
Presentation (Apps/)  ←→  Application (Contexts/.../Application/)  ←→  Domain (Contexts/.../Domain/)  ←→  Infrastructure (Contexts/.../Infrastructure/)
```

### 2. CQRS (Command Query Responsibility Segregation)

**Commands** (Escritura):
- Crean nuevos datos
- Modifican estado
- Registran eventos
- Lanzan excepciones

**Queries** (Lectura):
- Solo leen datos
- Sin efectos secundarios
- Retornan responses
- Nunca lanzan excepciones

### 3. Inyección de Dependencias

Todo se inyecta automáticamente gracias a:
```yaml
App\:
    resource: "../src/"
```

Solo hay que:
1. Implementar las interfaces correctas
2. Declarar dependencias en `__construct()`

### 4. Validación Multinivel

```
HTTP Request
    ↓ (Symfony Validator)
Request DTO
    ↓ (Lógica de DTO)
Value Objects
    ↓ (Constructor)
Domain Logic
    ↓ (Use Case)
Persistencia
```

### 5. Autenticación Integrada

```yaml
defaults: { auth: true }
```

Automáticamente:
1. Valida JWT
2. Extrae sessionId
3. Lo inyecta como parámetro del controller

---

## Comparación: Antes vs Después

### Antes (sin arquitectura clara)
```php
// Controller caótico
function createPost($request) {
    // Validación
    if (empty($request->body)) throw new Error(...);
    
    // Búsqueda de usuario
    $user = User::find($request->sessionId);
    
    // Creación
    $post = new Post();
    $post->body = $request->body;
    $post->user_id = $user->id;
    
    // Subida de recursos
    foreach ($request->resources as $res) {
        moveFile($res);
    }
    
    // Persistencia
    $post->save();
    
    // Notificación
    sendNotification(...);
    
    return success();
}
// 50+ líneas, difícil de testear, acoplado
```

### Después (con DDD/CQRS)
```php
// Controller limpio
public function __invoke(Request $request, string $id, string $sessionId): Response
{
    $input = CreatePostRequest::fromHttp($request, $id, $sessionId);
    $this->validateRequest($input);
    
    $command = $input->toCommand();
    $this->commandBus->dispatch($command);
    
    return $this->successEmptyResponse();
}

// Handler
public function __invoke(CreatePostCommand $command): void
{
    $this->creator->__invoke(
        new Uuid($command->id),
        new BodyValue($command->body),
        $this->userRepository->findById(new Uuid($command->userId)),
        $command->resources,
        null
    );
}

// Use Case
public function __invoke(Uuid $id, BodyValue $body, User $user, array $resources, ?Uuid $sharedPostId): void
{
    $post = Post::create($id, $body, $user, $resources, $sharedPostId);
    $this->repository->save($post);
}
```

**Beneficios:**
- 5-10 líneas por clase
- Cada clase tiene una responsabilidad
- Fácil de testear
- Fácil de entender
- Fácil de mantener
- Reutilizable

---

## Estructura Mental

```
┌─────────────────────────────────────────────────────────────┐
│                    USUARIO/CLIENTE                           │
│                   (Hace request HTTP)                        │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│         APLICACIÓN (Apps/Web/Post/)                         │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ Controller: Orquestar request                        │   │
│  │ - Recibe parámetros                                 │   │
│  │ - Crea DTO                                          │   │
│  │ - Valida                                            │   │
│  │ - Crea Command/Query                                │   │
│  │ - Dispone/ejecuta                                   │   │
│  └──────────────────────────────────────────────────────┘   │
│                         │                                     │
│  ┌──────────────────────┴──────────────────────────────┐    │
│  │ Request DTO: Mapear y validar datos HTTP          │    │
│  └───────────────────────────────────────────────────┘    │
└────────────────┬───────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────┐
│      CQRS (Commands/Queries)                                │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ Command: Objeto que transporta datos para escribir  │   │
│  │ CommandHandler: Ejecuta el comando                  │   │
│  │ Query: Objeto que transporta parámetros de lectura  │   │
│  │ QueryHandler: Ejecuta la query                      │   │
│  └──────────────────────────────────────────────────────┘   │
│                         │                                     │
│  ┌──────────────────────┴──────────────────────────────┐    │
│  │ Use Cases: Lógica de negocio                        │    │
│  │ - PostCreator                                       │    │
│  │ - PostSearcher                                      │    │
│  │ - PostLiker                                         │    │
│  └───────────────────────────────────────────────────┘    │
└────────────────┬───────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────┐
│      DOMINIO (Contexts/Web/Post/Domain/)                    │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ Agregados: Post (raíz)                              │   │
│  │ Entidades: Comment, Like, PostResource              │   │
│  │ Value Objects: BodyValue, CommentValue              │   │
│  │ Repositories: PostRepository (interfaz)             │   │
│  │ Events: PostCreatedDomainEvent                      │   │
│  │ Exceptions: PostNotFoundException                   │   │
│  └──────────────────────────────────────────────────────┘   │
│      ↓                                                        │
│      REGLAS DE NEGOCIO (Body entre 1-5000 caracteres, etc) │
└────────────────┬───────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────┐
│   INFRAESTRUCTURA (Contexts/Web/Post/Infrastructure/)       │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ MysqlPostRepository: Acceso a base de datos         │   │
│  │ MysqlCommentRepository: Persistencia                │   │
│  │ MysqlLikeRepository: Persistencia                   │   │
│  └──────────────────────────────────────────────────────┘   │
│                         │                                     │
│  ┌──────────────────────┴──────────────────────────────┐    │
│  │ Response DTO: Serializar datos para JSON            │    │
│  └───────────────────────────────────────────────────┘    │
└────────────────┬───────────────────────────────────────────┘
                 │
                 ▼
        DATABASE / API JSON RESPONSE
```

---

## Checklist de Comprensión

Si entiendes todos estos conceptos, estás listo:

```
[ ] ¿Por qué existe el JwtAuthMiddleware?
    → Para validar JWT y extraer sessionId automáticamente

[ ] ¿Qué es un Value Object y por qué se usa?
    → Encapsula validación. BodyValue valida en constructor.

[ ] ¿Cuál es la diferencia entre Command y Query?
    → Commands modifican estado, Queries solo leen.

[ ] ¿Qué hace un Handler?
    → Convierte datos primitivos a Value Objects y ejecuta use case.

[ ] ¿Qué es un Domain Event?
    → Representa algo importante que pasó en el agregado.

[ ] ¿Dónde va la validación de usuario propietario?
    → En el Use Case, no en el controller.

[ ] ¿Cómo se testea un endpoint completo?
    → Integration test: request HTTP → verificar BD.

[ ] ¿Qué pasa si lanzo excepción en Value Object constructor?
    → Se propaga al controller → ExceptionListener → HTTP response.

[ ] ¿Por qué Response tiene un método toArray()?
    → Para convertir a JSON en la respuesta HTTP.

[ ] ¿Cómo se inyectan dependencias?
    → Automáticamente por tipo en __construct().
```

---

## Glosario Rápido

| Término | Definición |
|---------|-----------|
| **Agregado** | Grupo de objetos tratados como unidad (Post con sus Comments, Likes) |
| **Value Object** | Objeto que representa un valor (BodyValue representa el texto del post) |
| **Repository** | Interfaz para acceder a datos (abstrae BD) |
| **Use Case** | Caso de uso de negocio (crear post, dar like) |
| **Domain Event** | Evento que representa algo importante (post creado) |
| **Command** | Acción que modifica estado |
| **Query** | Acción que lee datos |
| **Handler** | Ejecuta un Command o Query |
| **DTO** | Data Transfer Object (transporta datos entre capas) |
| **Value Object** | Objeto que no cambia después de crearse (inmutable) |
| **CQRS** | Command Query Responsibility Segregation (separar lectura/escritura) |
| **DDD** | Domain Driven Design (enfocarse en dominio de negocio) |
| **Middleware** | Código que se ejecuta antes de los controllers |
| **JWT** | JSON Web Token (para autenticación) |
| **Autowiring** | Inyección automática de dependencias |

---

## Próximos Pasos

### Si quieres aprender más:

1. **DDD (Domain-Driven Design)**
   - Lee "Domain-Driven Design" de Eric Evans
   - Entiende aggregates, value objects, repositories

2. **CQRS (Command Query Responsibility Segregation)**
   - Entiende por qué separar Commands de Queries
   - Aprende sobre event sourcing

3. **Testing**
   - Lee sobre testing en arquitectura DDD
   - Aprende a testear use cases, handlers, value objects

4. **Event Sourcing**
   - Complemento a Domain Events
   - Almacenar eventos en lugar de estado final

### Si quieres implementar otro bounded context:

1. Copia la estructura de Post
2. Adapta nombres (User, Tournament, Team, etc.)
3. Aplica los mismos patrones
4. Sigue el checklist de IMPLEMENTATION_CHECKLIST.md

---

## Contacto y Dudas

Para entender mejor:

1. Revisa los archivos fuente mencionados
2. Busca ejemplos similares en otros bounded contexts
3. Estudia los tests unitarios
4. Ejecuta requests HTTP y observa el flujo

---

## Resumen Final

La arquitectura DDD/CQRS del bounded context de Post proporciona:

✓ **Claridad**: Cada clase tiene una responsabilidad clara
✓ **Testabilidad**: Todo es inyectable y mockeable
✓ **Mantenibilidad**: Cambios localizados, bajo impacto
✓ **Escalabilidad**: Patrones se repiten en todos los contextos
✓ **Reusabilidad**: Componentes desacoplados
✓ **Expresividad**: El código expresa intenciones de negocio

Esta documentación te proporciona todo lo necesario para:
- Entender la arquitectura existente
- Implementar nuevos endpoints
- Mantener consistencia con los patrones
- Escribir código de calidad

¡Ahora estás listo para trabajar con el bounded context de Post!

---

## Índice de Documentos

1. **BOUNDED_CONTEXT_POST_GUIDE.md** - Guía principal (arquitectura, patrones, ejemplos)
2. **POST_CONTEXT_ADVANCED_PATTERNS.md** - Patrones avanzados (Value Objects, Events, Testing)
3. **IMPLEMENTATION_CHECKLIST.md** - Guía paso a paso para nuevos endpoints
4. **POST_CONTEXT_FILE_REFERENCE.md** - Referencia de archivos y estructura
5. **DOCUMENTATION_SUMMARY.md** - Este archivo (índice y resumen)

---

**Última actualización:** 2024
**Proyecto:** JEE API
**Bounded Context:** Post
