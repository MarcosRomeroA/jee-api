# Caso de Uso: Login (Autenticación)

## Información del Endpoint
- **Ruta:** `/login`
- **Método HTTP:** `POST`
- **Autenticación requerida:** No

## Descripción
Permite a un usuario autenticarse en el sistema proporcionando sus credenciales (email y contraseña).

## Request Body
```json
{
  "email": "usuario@ejemplo.com",
  "password": "MiPassword123!"
}
```

## Flujo Principal (Caso Exitoso)

1. El usuario proporciona email y contraseña
2. El sistema valida el formato del email
3. El sistema busca el usuario por email
4. El sistema verifica que la contraseña proporcionada coincide con la almacenada
5. El sistema genera:
   - Token JWT de acceso
   - Token JWT de refresh
   - Token de notificaciones Mercure
6. El sistema devuelve los tokens al usuario

### Response Exitoso (200)
```json
{
  "id": "uuid-del-usuario",
  "notificationToken": "token-mercure",
  "token": "jwt-access-token",
  "refreshToken": "jwt-refresh-token"
}
```

## Flujos Alternativos y Excepciones

### 1. Email con formato inválido
**Excepción:** `InvalidEmailException`
- **Cuándo ocurre:** El email proporcionado no cumple con el formato válido
- **Código HTTP:** 400
- **Mensaje:** El email no tiene un formato válido

### 2. Usuario no encontrado o contraseña incorrecta
**Excepción:** `UnauthorizedException`
- **Cuándo ocurre:** 
  - El usuario con ese email no existe en el sistema
  - La contraseña proporcionada no coincide con la almacenada
- **Código HTTP:** 401
- **Mensaje:** Credenciales incorrectas

### 3. Email no confirmado
**Flujo:** El login podría tener éxito pero el usuario podría tener restricciones si su email no está confirmado (depende de la lógica de negocio implementada)

## Validaciones

1. **Email:**
   - Debe tener formato válido (validación FILTER_VALIDATE_EMAIL)
   - Campo requerido

2. **Password:**
   - Campo requerido
   - Debe coincidir con la contraseña hasheada en la base de datos

## Consideraciones de Seguridad

- Las contraseñas se almacenan hasheadas usando BCRYPT
- No se debe revelar si el error es por usuario inexistente o contraseña incorrecta (ambos devuelven UnauthorizedException)
- Los tokens JWT tienen tiempo de expiración
- El refresh token es de larga duración y permite obtener nuevos access tokens
