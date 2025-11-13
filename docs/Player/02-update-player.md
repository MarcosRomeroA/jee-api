# Caso de Uso: Actualizar Perfil de Jugador

## Información del Endpoint
- **Ruta:** `/player/{id}`
- **Método HTTP:** `PUT`
- **Autenticación requerida:** Sí

## Request Body
```json
{
  "gameRankId": "uuid-nuevo-rango",
  "gameRoleId": "uuid-nuevo-rol",
  "username": "nuevo-username"
}
```

## Flujo Principal
1. Usuario autenticado actualiza su perfil de jugador
2. Sistema valida que el jugador pertenece al usuario
3. Sistema valida nuevos rango y rol
4. Sistema actualiza perfil

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido/no es dueño
- **PlayerNotFoundException (404):** Jugador no encontrado
- **GameRankNotFoundException (404):** Rango no encontrado
- **GameRoleNotFoundException (404):** Rol no encontrado
