# Caso de Uso: Agregar Equipo a Torneo

## Información del Endpoint
- **Ruta:** `/tournament/{tournamentId}/team/{teamId}`
- **Método HTTP:** `POST`
- **Autenticación requerida:** Sí

## Flujo Principal
1. Usuario registra equipo en torneo
2. Sistema valida que torneo existe y está OPEN
3. Sistema valida que equipo existe
4. Sistema valida que usuario es dueño del equipo
5. Sistema valida que torneo no está lleno
6. Sistema valida que equipo es del mismo juego
7. Sistema registra equipo en torneo

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido/no es dueño
- **TournamentNotFoundException (404):** Torneo no encontrado
- **TeamNotFoundException (404):** Equipo no encontrado
- **InvalidTournamentStateException (400):** Torneo no está OPEN
- **TournamentFullException (400):** Torneo lleno
- **TeamAlreadyRegisteredException (409):** Equipo ya registrado
- **GameNotFoundException (404):** Juegos no coinciden
