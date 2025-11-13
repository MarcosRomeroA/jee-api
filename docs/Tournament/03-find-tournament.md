# Caso de Uso: Buscar Torneo

## Información del Endpoint
- **Ruta:** `/tournament/{id}`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Flujo Principal
Obtiene información detallada del torneo.

## Response Exitoso (200)
```json
{
  "id": "uuid",
  "name": "Torneo Verano 2024",
  "description": "Descripción",
  "game": {
    "id": "uuid",
    "name": "League of Legends"
  },
  "organizer": {
    "id": "uuid",
    "username": "organizador"
  },
  "startDate": "2024-06-01T10:00:00Z",
  "endDate": "2024-06-30T20:00:00Z",
  "status": "OPEN",
  "maxTeams": 16,
  "registeredTeams": 8,
  "teams": [
    {
      "id": "uuid",
      "name": "Team 1"
    }
  ],
  "rules": "Reglas...",
  "createdAt": "2024-01-15T10:30:00Z"
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **TournamentNotFoundException (404):** Torneo no encontrado
- **InvalidUuidException (400):** UUID inválido
