# Caso de Uso: Seguir Usuario

## Información del Endpoint
- **Ruta:** `/user/{id}/follow`
- **Método HTTP:** `PUT`
- **Autenticación requerida:** Sí (JWT Token)

## Descripción
Permite a un usuario autenticado seguir a otro usuario de la plataforma.

## Parámetros de URL
- `id`: UUID del usuario a seguir

## Headers Requeridos
```
Authorization: Bearer {jwt-token}
```

## Flujo Principal (Caso Exitoso)

1. El usuario autenticado indica a quién desea seguir
2. El sistema valida el token JWT
3. El sistema valida que el ID es un UUID válido
4. El sistema busca al usuario que seguirá (follower)
5. El sistema busca al usuario a seguir (followed)
6. El sistema verifica que no se esté intentando seguir a sí mismo
7. El sistema crea la relación de seguimiento
8. El sistema genera una notificación para el usuario seguido
9. El sistema guarda los cambios

### Response Exitoso (200)
```json
{}
```

## Flujos Alternativos y Excepciones

### 1. Usuario no autenticado
**Excepción:** `UnauthorizedException`
- **Cuándo ocurre:** Token inválido, expirado o no proporcionado
- **Código HTTP:** 401

### 2. UUID inválido
**Excepción:** `InvalidUuidException`
- **Cuándo ocurre:** El ID proporcionado no es un UUID válido
- **Código HTTP:** 400

### 3. Usuario a seguir no encontrado
**Excepción:** `UserNotFoundException`
- **Cuándo ocurre:** No existe un usuario con ese ID
- **Código HTTP:** 404

### 4. Intentar seguirse a sí mismo
**Excepción:** `OtherUserIsMeException`
- **Cuándo ocurre:** El usuario intenta seguirse a sí mismo
- **Código HTTP:** 400
- **Mensaje:** No puedes seguirte a ti mismo

### 5. Ya sigue al usuario
**Comportamiento:** Dependiendo de la implementación:
- **Opción A:** Lanzar excepción (FollowAlreadyExistsException)
- **Opción B:** Operación idempotente (no hace nada, retorna éxito)

## Validaciones

1. **Token JWT:** Válido y no expirado
2. **ID:** UUID válido del usuario a seguir
3. **Usuarios existen:** Ambos usuarios deben existir
4. **No auto-seguimiento:** El usuario no puede seguirse a sí mismo

## Eventos Generados

- **UserFollowed:** Evento de dominio que dispara:
  - Creación de notificación para el usuario seguido
  - Actualización de contadores

## Consideraciones

- La operación es asíncrona en términos de notificaciones
- Los contadores de seguidores se actualizan inmediatamente
- El usuario seguido recibe una notificación
- No hay límite de usuarios que se pueden seguir (salvo límites de negocio si existen)
