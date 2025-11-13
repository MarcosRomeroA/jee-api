# Caso de Uso: Confirmar Email

## Información del Endpoint
- **Ruta:** `/auth/confirm-email/{token}`
- **Método HTTP:** `GET`
- **Autenticación requerida:** No

## Descripción
Permite confirmar el email de un usuario mediante un token único enviado por correo electrónico durante el registro.

## Parámetros de URL
- `token`: Token único de confirmación enviado al email del usuario

## Ejemplo de URL
```
GET /auth/confirm-email/abc123def456ghi789jkl012mno345pqr678
```

## Flujo Principal (Caso Exitoso)

1. El usuario hace clic en el enlace del email de confirmación
2. El sistema recibe el token de confirmación
3. El sistema busca la confirmación de email por el token
4. El sistema verifica que el token no haya sido usado previamente
5. El sistema verifica que el token no haya expirado
6. El sistema marca el email como confirmado
7. El sistema guarda el cambio en la base de datos
8. El sistema devuelve respuesta exitosa

### Response Exitoso (200)
```json
{}
```

## Flujos Alternativos y Excepciones

### 1. Token no encontrado
**Excepción:** `EmailConfirmationNotFoundException`
- **Cuándo ocurre:** 
  - El token proporcionado no existe en el sistema
  - El token es inválido o ha sido manipulado
- **Código HTTP:** 404
- **Mensaje:** Token de confirmación no encontrado

### 2. Email ya confirmado
**Excepción:** `EmailAlreadyConfirmedException`
- **Cuándo ocurre:** El usuario ya confirmó su email previamente
- **Código HTTP:** 409
- **Mensaje:** El email ya ha sido confirmado
- **Acción:** El usuario puede proceder a iniciar sesión

### 3. Token expirado
**Excepción:** `EmailConfirmationExpiredException`
- **Cuándo ocurre:** Ha pasado el tiempo límite para confirmar el email (típicamente 24-48 horas)
- **Código HTTP:** 410
- **Mensaje:** El token de confirmación ha expirado
- **Acción requerida:** El usuario debe solicitar un nuevo email de confirmación

## Validaciones

1. **Token:**
   - Campo requerido (parámetro de URL)
   - Debe existir en la base de datos
   - No debe haber sido usado previamente
   - No debe estar expirado

## Estado del Email

El sistema mantiene un registro de confirmación de email con los siguientes estados:
- **Pendiente:** Token generado pero no usado
- **Confirmado:** Email confirmado exitosamente
- **Expirado:** Token ha superado el tiempo límite

## Consideraciones

- El token de confirmación típicamente expira después de 24-48 horas
- Cada confirmación de email tiene un token único e irrepetible
- Una vez confirmado, el token no puede reutilizarse
- Si el token expira, el usuario debe solicitar un nuevo email de confirmación
- La confirmación del email puede ser requerida para ciertas funcionalidades del sistema
