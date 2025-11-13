# Caso de Uso: Buscar Partidos de Torneo

## Información del Endpoint
- **Ruta:** `/tournament/{tournamentId}/matches`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Query Parameters
- `round`: Filtrar por ronda
- `status`: Filtrar por estado
- `page`: Página

## Flujo Principal
Obtiene partidos de un torneo.

## Response Exitoso (200)
```json
{
  "matches": [
    {
      "id": "uuid",
      "team1": {...},
      "team2": {...},
      "scheduledDate": "...",
      "status": "PENDING",
      "round": "QUARTERFINALS"
    }
  ],
  "pagination": {...}
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **TournamentNotFoundException (404):** Torneo no encontrado
