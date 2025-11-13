# Caso de Uso: Refresh Token (Renovación de Token)

## Información del Endpoint
- **Ruta:** `/refresh-token`
- **Método HTTP:** `POST`
- **Autenticación requerida:** No (pero requiere un refresh token válido)

## Descripción
Permite obtener un nuevo token de acceso y refresh token utilizando un refresh token válido, sin necesidad de volver a proporcionar credenciales.

## Request Body
```json
{
  "refreshToken": "jwt-refresh-token-existente"
}
```

## Flujo Principal (Caso Exitoso)

1. El usuario proporciona un refresh token válido
2. El sistema decodifica el refresh token
3. El sistema verifica que el token tenga la bandera `refresh: true`
4. El sistema extrae el ID del usuario del token
5. El sistema genera:
   - Un nuevo token JWT de acceso
   - Un nuevo token JWT de refresh
6. El sistema devuelve los nuevos tokens al usuario

### Response Exitoso (200)
```json
{
  "token": "nuevo-jwt-access-token",
  "refreshToken": "nuevo-jwt-refresh-token"
}
```

## Flujos Alternativos y Excepciones

### 1. Token no es un refresh token
**Excepción:** `TokenIsNotRefreshTokenException`
- **Cuándo ocurre:** El token proporcionado no tiene la bandera `refresh: true` (es un access token normal)
- **Código HTTP:** 400
- **Mensaje:** El token proporcionado no es un refresh token

### 2. Token expirado
**Excepción:** `ExpiredTokenException`
- **Cuándo ocurre:** El refresh token ha expirado
- **Código HTTP:** 401
- **Mensaje:** El token ha expirado
- **Acción requerida:** El usuario debe realizar login nuevamente

### 3. Token inválido o malformado
**Excepción:** `JWTDecodeException`
- **Cuándo ocurre:** 
  - El token no puede ser decodificado
  - El token tiene una firma inválida
  - El formato del token es incorrecto
- **Código HTTP:** 401
- **Mensaje:** Token inválido

### 4. Token no proporcionado o vacío
**Excepción:** `ValidationException`
- **Cuándo ocurre:** No se proporciona el refresh token en el request
- **Código HTTP:** 400
- **Mensaje:** El campo refreshToken es requerido

## Validaciones

1. **Refresh Token:**
   - Campo requerido
   - Debe ser un JWT válido
   - Debe tener la bandera `refresh: true`
   - No debe estar expirado
   - Debe tener una firma válida

## Consideraciones de Seguridad

- Los refresh tokens tienen mayor duración que los access tokens
- Cada renovación genera un nuevo par de tokens (access + refresh)
- Se recomienda invalidar el refresh token anterior después de usarlo (si se implementa lista negra)
- Los tokens contienen solo el ID del usuario, no información sensible
- La firma JWT garantiza que el token no ha sido alterado
