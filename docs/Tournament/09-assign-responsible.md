# Caso de Uso: Asignar Responsable

## Información del Endpoint
- **Ruta:** `/tournament/{tournamentId}/responsible/{userId}`
- **Método HTTP:** `POST`
- **Autenticación requerida:** Sí

## Flujo Principal
1. Organizador asigna responsable al torneo
2. Sistema valida que usuario es organizador
3. Sistema valida que responsable existe
4. Sistema asigna responsable

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** No es organizador
- **TournamentNotFoundException (404):** Torneo no encontrado
- **UserNotFoundException (404):** Usuario no encontrado

## Consideraciones
- Responsable puede gestionar torneo junto con organizador
