# Caso de Uso: Dejar de Seguir Usuario

## Información del Endpoint
- **Ruta:** `/user/{id}/unfollow`
- **Método HTTP:** `PUT`
- **Autenticación requerida:** Sí

## Descripción
Permite a un usuario autenticado dejar de seguir a otro usuario.

## Flujo Principal
1. Usuario autenticado indica a quién dejar de seguir
2. Sistema valida token y UUID
3. Sistema busca ambos usuarios
4. Sistema elimina la relación de seguimiento
5. Sistema actualiza contadores

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido/expirado
- **InvalidUuidException (400):** UUID inválido
- **UserNotFoundException (404):** Usuario no encontrado
- **OtherUserIsMeException (400):** Intentar dejar de seguirse a sí mismo

## Consideraciones
- Operación idempotente: Si no seguía al usuario, no hace nada
- Actualiza contadores inmediatamente
