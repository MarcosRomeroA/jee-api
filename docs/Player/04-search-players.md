# Caso de Uso: Buscar Jugadores

## Información del Endpoint
- **Ruta:** `/players`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Query Parameters
- `gameId`: Filtrar por juego
- `rankId`: Filtrar por rango
- `roleId`: Filtrar por rol
- `username`: Buscar por username
- `page`: Página
- `limit`: Resultados por página

## Flujo Principal
Búsqueda de jugadores con filtros múltiples.

## Response Exitoso (200)
```json
{
  "players": [
    {
      "id": "uuid",
      "username": "jugador1",
      "game": {...},
      "rank": {...},
      "role": {...}
    }
  ],
  "pagination": {...}
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
