# Documentación de Casos de Uso - Juga en Equipo API

Esta documentación contiene todos los casos de uso organizados por agregado/entidad del sistema.

## Estructura de la Documentación

La documentación está organizada por carpetas, donde cada carpeta representa un agregado o entidad principal del dominio:

### 📁 Auth (Autenticación)
Casos de uso relacionados con autenticación y autorización de usuarios.
- [01 - Login](./Auth/01-login.md) - Autenticación de usuario
- [02 - Refresh Token](./Auth/02-refresh-token.md) - Renovación de tokens
- [03 - Confirm Email](./Auth/03-confirm-email.md) - Confirmación de email
- [04 - Resend Email Confirmation](./Auth/04-resend-email-confirmation.md) - Reenvío de confirmación

### 📁 User (Usuario)
Casos de uso relacionados con la gestión de usuarios y perfiles.
- [01 - Create User](./User/01-create-user.md) - Registro de usuario
- [02 - Find User](./User/02-find-user.md) - Buscar usuario por ID
- [03 - Update User](./User/03-update-user.md) - Actualizar perfil
- [04 - Follow User](./User/04-follow-user.md) - Seguir usuario
- [05 - Unfollow User](./User/05-unfollow-user.md) - Dejar de seguir
- [06 - Update Password](./User/06-update-password.md) - Cambiar contraseña
- [07 - Restore Password](./User/07-restore-password.md) - Recuperar contraseña
- [08 - Update Profile Photo](./User/08-update-profile-photo.md) - Actualizar foto
- [09 - Search Users](./User/09-search-users.md) - Buscar usuarios
- [10 - Find by Username](./User/10-find-by-username.md) - Buscar por username
- [11 - Followers](./User/11-followers.md) - Obtener seguidores
- [12 - Followings](./User/12-followings.md) - Obtener seguidos

### 📁 Player (Jugador)
Casos de uso relacionados con perfiles de jugadores por juego.
- [01 - Create Player](./Player/01-create-player.md) - Crear perfil de jugador
- [02 - Update Player](./Player/02-update-player.md) - Actualizar perfil
- [03 - Find Player](./Player/03-find-player.md) - Buscar jugador
- [04 - Search Players](./Player/04-search-players.md) - Buscar jugadores
- [05 - Delete Player](./Player/05-delete-player.md) - Eliminar perfil
- [06 - Verify Rank](./Player/06-verify-rank.md) - Verificar rango

### 📁 Team (Equipo)
Casos de uso relacionados con la gestión de equipos.
- [01 - Create Team](./Team/01-create-team.md) - Crear equipo
- [02 - Update Team](./Team/02-update-team.md) - Actualizar equipo
- [03 - Find Team](./Team/03-find-team.md) - Buscar equipo
- [04 - Search Teams](./Team/04-search-teams.md) - Buscar equipos
- [05 - Search My Teams](./Team/05-search-my-teams.md) - Mis equipos
- [06 - Delete Team](./Team/06-delete-team.md) - Eliminar equipo
- [07 - Request Access](./Team/07-request-access.md) - Solicitar acceso
- [08 - Accept Request](./Team/08-accept-request.md) - Aceptar solicitud

### 📁 Game (Juego)
Casos de uso relacionados con juegos disponibles.
- [01 - Search Games](./Game/01-search-games.md) - Buscar juegos
- [02 - Find Game](./Game/02-find-game.md) - Buscar juego por ID

### 📁 Tournament (Torneo)
Casos de uso relacionados con torneos y partidos.
- [01 - Create Tournament](./Tournament/01-create-tournament.md) - Crear torneo
- [02 - Update Tournament](./Tournament/02-update-tournament.md) - Actualizar torneo
- [03 - Find Tournament](./Tournament/03-find-tournament.md) - Buscar torneo
- [04 - Search Open Tournaments](./Tournament/04-search-open-tournaments.md) - Torneos abiertos
- [05 - Search My Tournaments](./Tournament/05-search-my-tournaments.md) - Mis torneos
- [06 - Delete Tournament](./Tournament/06-delete-tournament.md) - Eliminar torneo
- [07 - Add Team](./Tournament/07-add-team.md) - Agregar equipo
- [08 - Remove Team](./Tournament/08-remove-team.md) - Remover equipo
- [09 - Assign Responsible](./Tournament/09-assign-responsible.md) - Asignar responsable
- [10 - Create Match](./Tournament/10-create-match.md) - Crear partido
- [11 - Find Match](./Tournament/11-find-match.md) - Buscar partido
- [12 - Search Matches](./Tournament/12-search-matches.md) - Buscar partidos
- [13 - Start Match](./Tournament/13-start-match.md) - Iniciar partido
- [14 - Update Match Result](./Tournament/14-update-match-result.md) - Actualizar resultado
- [15 - Delete Match](./Tournament/15-delete-match.md) - Eliminar partido

### 📁 Post (Publicación)
Casos de uso relacionados con publicaciones y feed social.
- [01 - Create Post](./Post/01-create-post.md) - Crear publicación
- [02 - Find Post](./Post/02-find-post.md) - Buscar publicación
- [03 - Search Posts](./Post/03-search-posts.md) - Buscar publicaciones
- [04 - Search My Feed](./Post/04-search-my-feed.md) - Mi feed
- [05 - Like Post](./Post/05-like-post.md) - Dar like
- [06 - Dislike Post](./Post/06-dislike-post.md) - Quitar like
- [07 - Delete Post](./Post/07-delete-post.md) - Eliminar publicación
- [08 - Add Comment](./Post/08-add-comment.md) - Agregar comentario
- [09 - Search Comments](./Post/09-search-comments.md) - Buscar comentarios
- [10 - Add Temp Resource](./Post/10-add-temp-resource.md) - Subir recurso temporal

### 📁 Notification (Notificación)
Casos de uso relacionados con notificaciones.
- [01 - Search Notifications](./Notification/01-search-notifications.md) - Buscar notificaciones
- [02 - Mark as Read](./Notification/02-mark-as-read.md) - Marcar como leída

### 📁 Conversation (Conversación)
Casos de uso relacionados con mensajería directa.
- [01 - Find Conversations](./Conversation/01-find-conversations.md) - Buscar conversaciones
- [02 - Find by Other User](./Conversation/02-find-by-other-user.md) - Conversación con usuario
- [03 - Create Message](./Conversation/03-create-message.md) - Crear mensaje
- [04 - Search Messages](./Conversation/04-search-messages.md) - Buscar mensajes

## Formato de Cada Caso de Uso

Cada documento de caso de uso incluye:

1. **Información del Endpoint**: Ruta, método HTTP, requisitos de autenticación
2. **Descripción**: Breve explicación del caso de uso
3. **Request/Response**: Ejemplos de entrada y salida
4. **Flujo Principal**: Pasos del caso de éxito
5. **Flujos Alternativos y Excepciones**: Todos los caminos posibles de error con:
   - Tipo de excepción
   - Cuándo ocurre
   - Código HTTP
   - Mensaje
6. **Validaciones**: Reglas de negocio y validaciones aplicadas
7. **Consideraciones**: Información adicional relevante

## Excepciones Comunes

### Excepciones de Autenticación
- **UnauthorizedException (401)**: Token inválido, expirado o no proporcionado
- **ExpiredTokenException (401)**: Token JWT ha expirado

### Excepciones de Validación
- **ValidationException (400)**: Datos de entrada inválidos
- **InvalidUuidException (400)**: UUID con formato inválido
- **InvalidEmailException (400)**: Email con formato inválido

### Excepciones de Contraseña
- **PasswordMismatchException (400)**: Contraseñas no coinciden
- **PasswordMinimumLengthRequiredException (400)**: Contraseña muy corta (≤8 caracteres)
- **PasswordUppercaseRequiredException (400)**: Falta letra mayúscula
- **PasswordSpecialCharacterRequiredException (400)**: Falta carácter especial
- **CurrentPasswordMismatchException (400)**: Contraseña actual incorrecta

### Excepciones de Recursos No Encontrados (404)
- **UserNotFoundException**: Usuario no encontrado
- **PlayerNotFoundException**: Jugador no encontrado
- **TeamNotFoundException**: Equipo no encontrado
- **GameNotFoundException**: Juego no encontrado
- **TournamentNotFoundException**: Torneo no encontrado
- **MatchNotFoundException**: Partido no encontrado
- **PostNotFoundException**: Publicación no encontrada
- **NotificationNotFoundException**: Notificación no encontrada
- **ConversationNotFoundException**: Conversación no encontrada

### Excepciones de Conflicto (409)
- **EmailAlreadyExistsException**: Email ya registrado
- **UsernameAlreadyExistsException**: Username ya en uso
- **EmailAlreadyConfirmedException**: Email ya confirmado
- **TeamAlreadyRegisteredException**: Equipo ya registrado en torneo
- **RequestAlreadyExistsException**: Solicitud ya existe

### Excepciones de Estado Inválido (400)
- **InvalidTournamentStateException**: Operación no permitida en estado actual del torneo
- **InvalidMatchStateException**: Operación no permitida en estado actual del partido
- **TournamentFullException**: Torneo lleno, no admite más equipos

### Excepciones de Autorización (403)
- **PostDeletionNotAllowedException**: No autorizado para eliminar publicación
- **UserNotExistsInConversationException**: Usuario no es participante de conversación

### Excepciones de Negocio (400)
- **OtherUserIsMeException**: No se puede realizar acción sobre sí mismo
- **TextIsLongerThanAllowedException**: Texto excede longitud máxima
- **RankVerificationException**: Error al verificar rango con API externa

## Consideraciones de Seguridad

- Todas las contraseñas se almacenan hasheadas con BCRYPT
- Los tokens JWT tienen tiempo de expiración
- Los refresh tokens permiten renovar access tokens sin re-autenticación
- Los endpoints protegidos requieren token JWT válido
- Las operaciones de modificación validan que el usuario tenga permisos

## Tecnologías

- **Framework**: Symfony (PHP)
- **Autenticación**: JWT (LexikJWTAuthenticationBundle)
- **Base de datos**: MariaDB con Doctrine ORM
- **Mensajería**: RabbitMQ para eventos asíncronos
- **Notificaciones en tiempo real**: Mercure
- **Arquitectura**: DDD (Domain-Driven Design) con CQRS

## Contacto y Contribución

Para preguntas sobre la API o sugerencias de mejora en la documentación, contactar al equipo de desarrollo.
