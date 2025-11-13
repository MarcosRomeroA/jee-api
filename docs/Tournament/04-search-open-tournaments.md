# Caso de Uso: Buscar Torneos Abiertos

## Información del Endpoint
- **Ruta:** `/open-tournaments`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Query Parameters
- `gameId`: Filtrar por juego
- `page`: Página
- `limit`: Resultados por página

## Flujo Principal
Obtiene torneos en estado OPEN que aceptan registros.

## Response Exitoso (200)
```json
{
  "tournaments": [
    {
      "id": "uuid",
      "name": "Torneo 1",
      "game": {...},
      "startDate": "2024-06-01T10:00:00Z",
      "registeredTeams": 8,
      "maxTeams": 16,
      "spotsAvailable": 8
    }
  ],
  "pagination": {...}
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
