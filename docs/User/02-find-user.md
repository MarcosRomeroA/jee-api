# Caso de Uso: Buscar Usuario por ID

## Información del Endpoint
- **Ruta:** `/user/{id}`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí (JWT Token)

## Descripción
Permite obtener la información detallada de un usuario específico mediante su ID único.

## Parámetros de URL
- `id`: UUID del usuario a buscar

## Ejemplo de URL
```
GET /user/550e8400-e29b-41d4-a716-446655440000
```

## Headers Requeridos
```
Authorization: Bearer {jwt-token}
```

## Flujo Principal (Caso Exitoso)

1. El cliente proporciona un token JWT válido
2. El sistema valida la autenticación del usuario
3. El sistema valida el formato del UUID
4. El sistema busca el usuario por ID
5. El sistema devuelve la información del usuario

### Response Exitoso (200)
```json
{
  "id": "550e8400-e29b-41d4-a716-446655440000",
  "firstname": "Juan",
  "lastname": "Pérez",
  "username": "juanperez",
  "email": "juan@ejemplo.com",
  "profileImage": "https://...",
  "emailConfirmed": true,
  "createdAt": "2024-01-15T10:30:00Z",
  "followersCount": 150,
  "followingCount": 200
}
```

## Flujos Alternativos y Excepciones

### 1. Usuario no autenticado
**Excepción:** `UnauthorizedException`
- **Cuándo ocurre:** 
  - No se proporciona token JWT
  - El token es inválido
  - El token ha expirado
- **Código HTTP:** 401
- **Mensaje:** No autorizado

### 2. Token expirado
**Excepción:** `ExpiredTokenException`
- **Cuándo ocurre:** El token JWT ha excedido su tiempo de vida
- **Código HTTP:** 401
- **Mensaje:** Token expirado
- **Acción:** Usar refresh token para obtener nuevo token

### 3. UUID inválido
**Excepción:** `InvalidUuidException`
- **Cuándo ocurre:** El ID proporcionado no es un UUID válido
- **Código HTTP:** 400
- **Mensaje:** UUID inválido

### 4. Usuario no encontrado
**Excepción:** `UserNotFoundException`
- **Cuándo ocurre:** No existe un usuario con ese ID
- **Código HTTP:** 404
- **Mensaje:** Usuario no encontrado

## Validaciones

1. **Token JWT:**
   - Debe ser válido y no expirado
   - Campo requerido en header Authorization

2. **ID (UUID):**
   - Debe ser un UUID válido (formato UUID v4)
   - Campo requerido en URL

## Información Retornada

- **Información pública del usuario:**
  - ID único
  - Nombre y apellido
  - Username
  - Email
  - Foto de perfil
  - Estado de confirmación de email
  - Fecha de creación
  - Contadores de seguidores y seguidos

## Consideraciones

- Este endpoint retorna información pública del usuario
- Cualquier usuario autenticado puede ver la información de otros usuarios
- Los datos sensibles como la contraseña nunca se retornan
- El email puede o no ser visible dependiendo de la configuración de privacidad
