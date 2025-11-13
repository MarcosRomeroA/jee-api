# Caso de Uso: Restaurar Contraseña

## Información del Endpoint
- **Ruta:** `/user/{id}/restore`
- **Método HTTP:** `PUT`
- **Autenticación requerida:** No

## Descripción
Permite restaurar la contraseña de un usuario mediante un token enviado por email.

## Request Body
```json
{
  "token": "token-de-recuperacion",
  "newPassword": "NuevaPassword123!",
  "confirmationPassword": "NuevaPassword123!"
}
```

## Flujo Principal
1. Usuario proporciona token de restauración y nueva contraseña
2. Sistema valida token
3. Sistema valida contraseñas coinciden
4. Sistema valida requisitos de contraseña
5. Sistema actualiza contraseña
6. Sistema invalida token

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **InvalidUuidException (400):** UUID inválido
- **ExpiredTokenException (401):** Token expirado
- **UserNotFoundException (404):** Usuario no encontrado
- **PasswordMismatchException (400):** Contraseñas no coinciden
- **PasswordMinimumLengthRequiredException (400):** < 8 caracteres
- **PasswordUppercaseRequiredException (400):** Falta mayúscula
- **PasswordSpecialCharacterRequiredException (400):** Falta carácter especial

## Consideraciones
- Token expira típicamente en 24 horas
- Token solo puede usarse una vez
