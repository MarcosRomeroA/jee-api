# Resumen de Casos de Uso por Agregado

Este documento proporciona un resumen rápido de todos los casos de uso organizados por agregado.

## 📊 Estadísticas Generales

- **Total de agregados:** 9
- **Total de casos de uso:** 63
- **Total de endpoints documentados:** 63

## 📝 Distribución por Agregado

| Agregado | Casos de Uso | Descripción |
|----------|--------------|-------------|
| **Auth** | 4 | Autenticación y confirmación de email |
| **User** | 12 | Gestión de usuarios y perfiles |
| **Player** | 6 | Perfiles de jugadores por juego |
| **Team** | 8 | Gestión de equipos |
| **Game** | 2 | Catálogo de juegos |
| **Tournament** | 15 | Torneos y partidos |
| **Post** | 10 | Publicaciones y feed social |
| **Notification** | 2 | Sistema de notificaciones |
| **Conversation** | 4 | Mensajería directa |

## 🔐 Auth (4 casos de uso)

### Autenticación
1. **Login** - POST `/login`
   - Autenticar usuario con email y contraseña
   - Excepciones: InvalidEmail, Unauthorized

2. **Refresh Token** - POST `/refresh-token`
   - Renovar tokens de acceso
   - Excepciones: TokenIsNotRefreshToken, ExpiredToken, JWTDecode

### Confirmación de Email
3. **Confirm Email** - GET `/auth/confirm-email/{token}`
   - Confirmar email con token
   - Excepciones: EmailConfirmationNotFound, EmailAlreadyConfirmed, EmailConfirmationExpired

4. **Resend Email Confirmation** - POST `/auth/resend-confirmation`
   - Reenviar email de confirmación
   - Excepciones: InvalidEmail, UserNotFound, EmailAlreadyConfirmed

## 👤 User (12 casos de uso)

### Gestión de Perfil
1. **Create User** - POST `/user`
   - Registro de nuevo usuario
   - Excepciones: InvalidEmail, EmailAlreadyExists, UsernameAlreadyExists, PasswordMismatch, PasswordMinimumLength, PasswordUppercaseRequired, PasswordSpecialCharacterRequired

2. **Find User** - GET `/user/{id}`
   - Buscar usuario por ID
   - Excepciones: Unauthorized, UserNotFound, InvalidUuid

3. **Find by Username** - GET `/user/by-username/{username}`
   - Buscar usuario por username
   - Excepciones: Unauthorized, UserNotFound

4. **Search Users** - GET `/users`
   - Buscar usuarios con filtros
   - Excepciones: Unauthorized

5. **Update User** - PUT `/user`
   - Actualizar perfil de usuario
   - Excepciones: Unauthorized, InvalidEmail, EmailAlreadyExists, UsernameAlreadyExists, UserNotFound

### Contraseñas
6. **Update Password** - PUT `/user/password/{id}`
   - Cambiar contraseña
   - Excepciones: Unauthorized, CurrentPasswordMismatch, PasswordMismatch, PasswordValidations, UserNotFound

7. **Restore Password** - PUT `/user/{id}/restore`
   - Recuperar contraseña con token
   - Excepciones: InvalidUuid, ExpiredToken, UserNotFound, PasswordMismatch, PasswordValidations

### Foto de Perfil
8. **Update Profile Photo** - POST `/user-profile-image`
   - Actualizar foto de perfil
   - Excepciones: Unauthorized, ImageUploadFailed, Validation, UserNotFound

### Red Social
9. **Follow User** - PUT `/user/{id}/follow`
   - Seguir a un usuario
   - Excepciones: Unauthorized, InvalidUuid, UserNotFound, OtherUserIsMe

10. **Unfollow User** - PUT `/user/{id}/unfollow`
    - Dejar de seguir
    - Excepciones: Unauthorized, InvalidUuid, UserNotFound, OtherUserIsMe

11. **Followers** - GET `/user/{id}/followers`
    - Obtener seguidores
    - Excepciones: Unauthorized, UserNotFound, InvalidUuid

12. **Followings** - GET `/user/{id}/followings`
    - Obtener usuarios seguidos
    - Excepciones: Unauthorized, UserNotFound

## 🎮 Player (6 casos de uso)

1. **Create Player** - POST `/player`
   - Crear perfil de jugador
   - Excepciones: Unauthorized, GameNotFound, GameRankNotFound, GameRoleNotFound, InvalidUuid

2. **Update Player** - PUT `/player/{id}`
   - Actualizar perfil
   - Excepciones: Unauthorized, PlayerNotFound, GameRankNotFound, GameRoleNotFound

3. **Find Player** - GET `/player/{id}`
   - Buscar jugador
   - Excepciones: Unauthorized, PlayerNotFound, InvalidUuid

4. **Search Players** - GET `/players`
   - Buscar jugadores con filtros
   - Excepciones: Unauthorized

5. **Delete Player** - DELETE `/player/{id}`
   - Eliminar perfil de jugador
   - Excepciones: Unauthorized, PlayerNotFound, InvalidUuid

6. **Verify Rank** - POST `/player/verify-rank`
   - Verificar rango con API externa
   - Excepciones: Unauthorized, PlayerNotFound, RankVerification, GameNotFound

## 👥 Team (8 casos de uso)

### Gestión Básica
1. **Create Team** - POST `/team`
   - Crear equipo
   - Excepciones: Unauthorized, GameNotFound, UserNotFound, InvalidUuid

2. **Update Team** - PUT `/team/{id}`
   - Actualizar equipo
   - Excepciones: Unauthorized, TeamNotFound, InvalidUuid

3. **Find Team** - GET `/team/{id}`
   - Buscar equipo
   - Excepciones: Unauthorized, TeamNotFound, InvalidUuid

4. **Search Teams** - GET `/teams`
   - Buscar equipos
   - Excepciones: Unauthorized

5. **Search My Teams** - GET `/my-teams`
   - Mis equipos
   - Excepciones: Unauthorized

6. **Delete Team** - DELETE `/team/{id}`
   - Eliminar equipo
   - Excepciones: Unauthorized, TeamNotFound, InvalidTournamentState

### Membresía
7. **Request Access** - POST `/team/{teamId}/request-access`
   - Solicitar unirse a equipo
   - Excepciones: Unauthorized, TeamNotFound, RequestAlreadyExists, PlayerNotFound

8. **Accept Request** - POST `/team/request/{requestId}/accept`
   - Aceptar solicitud
   - Excepciones: Unauthorized, RequestNotFound, TeamNotFound, UserNotFound

## 🎯 Game (2 casos de uso)

1. **Search Games** - GET `/games`
   - Buscar juegos
   - Excepciones: Unauthorized

2. **Find Game** - GET `/game/{id}`
   - Buscar juego por ID
   - Excepciones: Unauthorized, GameNotFound, InvalidUuid

## 🏆 Tournament (15 casos de uso)

### Gestión de Torneos
1. **Create Tournament** - POST `/tournament`
   - Crear torneo
   - Excepciones: Unauthorized, GameNotFound, Validation, InvalidUuid

2. **Update Tournament** - PUT `/tournament/{id}`
   - Actualizar torneo
   - Excepciones: Unauthorized, TournamentNotFound, InvalidTournamentState, Validation

3. **Find Tournament** - GET `/tournament/{id}`
   - Buscar torneo
   - Excepciones: Unauthorized, TournamentNotFound, InvalidUuid

4. **Search Open Tournaments** - GET `/open-tournaments`
   - Torneos abiertos
   - Excepciones: Unauthorized

5. **Search My Tournaments** - GET `/my-tournaments`
   - Mis torneos
   - Excepciones: Unauthorized

6. **Delete Tournament** - DELETE `/tournament/{id}`
   - Eliminar torneo
   - Excepciones: Unauthorized, TournamentNotFound, InvalidTournamentState

### Equipos en Torneo
7. **Add Team** - POST `/tournament/{tournamentId}/team/{teamId}`
   - Agregar equipo
   - Excepciones: Unauthorized, TournamentNotFound, TeamNotFound, InvalidTournamentState, TournamentFull, TeamAlreadyRegistered, GameNotFound

8. **Remove Team** - DELETE `/tournament/{tournamentId}/team/{teamId}`
   - Remover equipo
   - Excepciones: Unauthorized, TournamentNotFound, TeamNotFound, TeamNotRegistered, InvalidTournamentState

9. **Assign Responsible** - POST `/tournament/{tournamentId}/responsible/{userId}`
   - Asignar responsable
   - Excepciones: Unauthorized, TournamentNotFound, UserNotFound

### Gestión de Partidos
10. **Create Match** - PUT `/match`
    - Crear partido
    - Excepciones: Unauthorized, TournamentNotFound, TeamNotFound, TeamNotRegistered

11. **Find Match** - GET `/match/{id}`
    - Buscar partido
    - Excepciones: Unauthorized, MatchNotFound

12. **Search Matches** - GET `/tournament/{tournamentId}/matches`
    - Buscar partidos
    - Excepciones: Unauthorized, TournamentNotFound

13. **Start Match** - POST `/match/{id}/start`
    - Iniciar partido
    - Excepciones: Unauthorized, MatchNotFound, InvalidMatchState

14. **Update Match Result** - PUT `/match/{id}/result`
    - Actualizar resultado
    - Excepciones: Unauthorized, MatchNotFound, InvalidMatchState, TeamNotFound

15. **Delete Match** - DELETE `/match/{id}`
    - Eliminar partido
    - Excepciones: Unauthorized, MatchNotFound, InvalidMatchState

## 📱 Post (10 casos de uso)

### Gestión de Publicaciones
1. **Create Post** - PUT `/post/{id}`
   - Crear publicación
   - Excepciones: Unauthorized, PostAlreadyExists, TextIsLongerThanAllowed, Validation

2. **Find Post** - GET `/post/{id}`
   - Buscar publicación
   - Excepciones: Unauthorized, PostNotFound, InvalidUuid

3. **Search Posts** - GET `/posts`
   - Buscar publicaciones
   - Excepciones: Unauthorized

4. **Search My Feed** - GET `/my-feed`
   - Mi feed
   - Excepciones: Unauthorized

5. **Delete Post** - DELETE `/post/{id}/delete`
   - Eliminar publicación
   - Excepciones: Unauthorized, PostNotFound, PostDeletionNotAllowed

### Interacciones
6. **Like Post** - PUT `/post/{id}/like`
   - Dar like
   - Excepciones: Unauthorized, PostNotFound

7. **Dislike Post** - PUT `/post/{id}/dislike`
   - Quitar like
   - Excepciones: Unauthorized, PostNotFound

### Comentarios
8. **Add Comment** - PUT `/post/{id}/comment`
   - Agregar comentario
   - Excepciones: Unauthorized, PostNotFound, TextIsLongerThanAllowed

9. **Search Comments** - GET `/post/{id}/comments`
   - Buscar comentarios
   - Excepciones: Unauthorized, PostNotFound

### Recursos
10. **Add Temp Resource** - POST `/post/{id}/resource`
    - Subir recurso temporal
    - Excepciones: Unauthorized, Validation, ImageUploadFailed

## 🔔 Notification (2 casos de uso)

1. **Search Notifications** - GET `/notifications`
   - Buscar notificaciones
   - Excepciones: Unauthorized

2. **Mark as Read** - PUT `/notification/{id}/mark-as-read`
   - Marcar como leída
   - Excepciones: Unauthorized, NotificationNotFound, InvalidUuid

## 💬 Conversation (4 casos de uso)

1. **Find Conversations** - GET `/conversations`
   - Buscar conversaciones
   - Excepciones: Unauthorized

2. **Find by Other User** - GET `/conversation/by-other-user/{id}`
   - Conversación con usuario
   - Excepciones: Unauthorized, ConversationNotFound, InvalidUuid, UserNotFound

3. **Create Message** - PUT `/conversation/{conversationId}/message/{messageId}`
   - Crear mensaje
   - Excepciones: Unauthorized, ConversationNotFound, UserNotExistsInConversation, UserNotFound, InvalidUuid, TextIsLongerThanAllowed

4. **Search Messages** - GET `/conversation/{conversationId}/messages`
   - Buscar mensajes
   - Excepciones: Unauthorized, ConversationNotFound, UserNotExistsInConversation, InvalidUuid

## 🔍 Índice de Excepciones

### Por Código HTTP

**400 - Bad Request (Validación)**
- InvalidEmailException
- InvalidUuidException
- PasswordMismatchException
- PasswordMinimumLengthRequiredException
- PasswordUppercaseRequiredException
- PasswordSpecialCharacterRequiredException
- CurrentPasswordMismatchException
- TextIsLongerThanAllowedException
- ValidationException
- OtherUserIsMeException
- InvalidTournamentStateException
- InvalidMatchStateException
- TournamentFullException
- RankVerificationException

**401 - Unauthorized (Autenticación)**
- UnauthorizedException
- ExpiredTokenException
- JWTDecodeException

**403 - Forbidden (Autorización)**
- PostDeletionNotAllowedException
- UserNotExistsInConversationException

**404 - Not Found (Recursos)**
- UserNotFoundException
- PlayerNotFoundException
- TeamNotFoundException
- GameNotFoundException
- TournamentNotFoundException
- MatchNotFoundException
- PostNotFoundException
- NotificationNotFoundException
- ConversationNotFoundException
- EmailConfirmationNotFoundException
- RequestNotFoundException
- CommentNotFoundException
- GameRankNotFoundException
- GameRoleNotFoundException

**409 - Conflict (Estado)**
- EmailAlreadyExistsException
- UsernameAlreadyExistsException
- EmailAlreadyConfirmedException
- PostAlreadyExistsException
- TeamAlreadyRegisteredException
- RequestAlreadyExistsException

**410 - Gone (Expirado)**
- EmailConfirmationExpiredException

**500 - Internal Server Error**
- ImageUploadFailedException
- JWTEncodeException

## 📖 Leyenda

- **✅ Success (200/201):** Operación exitosa
- **🔒 Auth Required:** Requiere autenticación JWT
- **🔓 Public:** No requiere autenticación
- **📝 CRUD:** Create, Read, Update, Delete
- **🔍 Query:** Solo lectura con filtros
- **⚡ Event:** Genera eventos de dominio

## 🔗 Referencias

Para más detalles de cada caso de uso, consultar los documentos individuales en sus respectivas carpetas.
