# Caso de Uso: Actualizar Contraseña

## Información del Endpoint
- **Ruta:** `/user/password/{id}`
- **Método HTTP:** `PUT`
- **Autenticación requerida:** Sí

## Request Body
```json
{
  "currentPassword": "PasswordActual123!",
  "newPassword": "NuevoPassword456!",
  "confirmationPassword": "NuevoPassword456!"
}
```

## Flujo Principal
1. Usuario proporciona contraseña actual y nueva
2. Sistema valida contraseña actual
3. Sistema valida que newPassword y confirmationPassword coincidan
4. Sistema valida requisitos de la nueva contraseña
5. Sistema hashea y guarda nueva contraseña

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **CurrentPasswordMismatchException (400):** Contraseña actual incorrecta
- **PasswordMismatchException (400):** Contraseñas nuevas no coinciden
- **PasswordMinimumLengthRequiredException (400):** < 8 caracteres
- **PasswordUppercaseRequiredException (400):** Falta mayúscula
- **PasswordSpecialCharacterRequiredException (400):** Falta carácter especial
- **UserNotFoundException (404):** Usuario no encontrado

## Validaciones
- Contraseña actual debe ser correcta
- Nueva contraseña: >8 caracteres, mayúscula, carácter especial
- Contraseñas nuevas deben coincidir
