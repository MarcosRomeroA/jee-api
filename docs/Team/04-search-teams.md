# Caso de Uso: Buscar Equipos

## Información del Endpoint
- **Ruta:** `/teams`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Query Parameters
- `gameId`: Filtrar por juego
- `name`: Buscar por nombre
- `page`: Página
- `limit`: Resultados por página

## Flujo Principal
Búsqueda de equipos con filtros.

## Response Exitoso (200)
```json
{
  "teams": [
    {
      "id": "uuid",
      "name": "Team 1",
      "game": {...},
      "membersCount": 5
    }
  ],
  "pagination": {...}
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
