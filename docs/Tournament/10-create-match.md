# Caso de Uso: Crear Partido

## Información del Endpoint
- **Ruta:** `/match`
- **Método HTTP:** `PUT`
- **Autenticación requerida:** Sí

## Request Body
```json
{
  "id": "uuid-generado",
  "tournamentId": "uuid-del-torneo",
  "team1Id": "uuid-equipo-1",
  "team2Id": "uuid-equipo-2",
  "scheduledDate": "2024-06-15T15:00:00Z",
  "round": "SEMIFINALS"
}
```

## Flujo Principal
1. Organizador crea partido entre dos equipos
2. Sistema valida permisos
3. Sistema valida que ambos equipos están en torneo
4. Sistema crea partido

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** No es organizador/responsable
- **TournamentNotFoundException (404):** Torneo no encontrado
- **TeamNotFoundException (404):** Equipo no encontrado
- **TeamNotRegisteredException (400):** Equipo no está en torneo
