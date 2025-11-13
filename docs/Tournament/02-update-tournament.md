# Caso de Uso: Actualizar Torneo

## Información del Endpoint
- **Ruta:** `/tournament/{id}`
- **Método HTTP:** `PUT`
- **Autenticación requerida:** Sí

## Request Body
```json
{
  "name": "Nuevo Nombre",
  "description": "Nueva descripción",
  "startDate": "2024-06-01T10:00:00Z",
  "endDate": "2024-06-30T20:00:00Z",
  "maxTeams": 20,
  "rules": "Nuevas reglas"
}
```

## Flujo Principal
1. Usuario autenticado actualiza torneo
2. Sistema valida que es organizador o responsable
3. Sistema valida estado del torneo
4. Sistema actualiza información

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** No es organizador/responsable
- **TournamentNotFoundException (404):** Torneo no encontrado
- **InvalidTournamentStateException (400):** Torneo ya iniciado/finalizado
- **ValidationException (400):** Datos inválidos
