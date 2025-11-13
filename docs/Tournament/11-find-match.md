# Caso de Uso: Buscar Partido

## Información del Endpoint
- **Ruta:** `/match/{id}`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Flujo Principal
Obtiene información de un partido.

## Response Exitoso (200)
```json
{
  "id": "uuid",
  "tournament": {...},
  "team1": {...},
  "team2": {...},
  "scheduledDate": "2024-06-15T15:00:00Z",
  "status": "PENDING",
  "result": {
    "winner": null,
    "team1Score": null,
    "team2Score": null
  },
  "round": "SEMIFINALS"
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **MatchNotFoundException (404):** Partido no encontrado
