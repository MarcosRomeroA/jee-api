# Caso de Uso: Crear Perfil de Jugador

## Información del Endpoint
- **Ruta:** `/player`
- **Método HTTP:** `POST`
- **Autenticación requerida:** Sí

## Descripción
Permite crear un perfil de jugador asociado a un juego específico, con información de su rango y rol.

## Request Body
```json
{
  "id": "uuid-generado",
  "gameId": "uuid-del-juego",
  "gameRankId": "uuid-del-rango",
  "gameRoleId": "uuid-del-rol",
  "username": "jugador123"
}
```

## Flujo Principal
1. Usuario autenticado crea perfil de jugador
2. Sistema valida que el juego existe
3. Sistema valida que el rango existe para ese juego
4. Sistema valida que el rol existe para ese juego
5. Sistema crea perfil de jugador
6. Sistema asocia jugador con usuario autenticado

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **GameNotFoundException (404):** Juego no encontrado
- **GameRankNotFoundException (404):** Rango no encontrado
- **GameRoleNotFoundException (404):** Rol no encontrado
- **InvalidUuidException (400):** UUID inválido
- **ValidationException (400):** Datos inválidos

## Validaciones
- Todos los UUIDs deben ser válidos
- Juego debe existir
- Rango debe pertenecer al juego
- Rol debe pertenecer al juego
- Username del jugador es requerido
