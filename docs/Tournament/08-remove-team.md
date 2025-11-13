# Caso de Uso: Remover Equipo de Torneo

## Información del Endpoint
- **Ruta:** `/tournament/{tournamentId}/team/{teamId}`
- **Método HTTP:** `DELETE`
- **Autenticación requerida:** Sí

## Flujo Principal
1. Usuario o organizador remueve equipo
2. Sistema valida permisos
3. Sistema valida estado del torneo
4. Sistema remueve equipo

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** Sin permisos
- **TournamentNotFoundException (404):** Torneo no encontrado
- **TeamNotFoundException (404):** Equipo no encontrado
- **TeamNotRegisteredException (404):** Equipo no está en torneo
- **InvalidTournamentStateException (400):** Torneo ya iniciado
