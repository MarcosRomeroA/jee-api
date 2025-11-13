# Caso de Uso: Crear Torneo

## Información del Endpoint
- **Ruta:** `/tournament`
- **Método HTTP:** `POST`
- **Autenticación requerida:** Sí

## Request Body
```json
{
  "id": "uuid-generado",
  "gameId": "uuid-del-juego",
  "name": "Torneo Verano 2024",
  "description": "Torneo profesional",
  "startDate": "2024-06-01T10:00:00Z",
  "endDate": "2024-06-30T20:00:00Z",
  "maxTeams": 16,
  "rules": "Reglas del torneo..."
}
```

## Flujo Principal
1. Usuario autenticado crea torneo
2. Sistema valida que juego existe
3. Sistema valida fechas
4. Sistema crea torneo con usuario como organizador
5. Sistema establece estado inicial (DRAFT o OPEN)

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **GameNotFoundException (404):** Juego no encontrado
- **ValidationException (400):** Datos inválidos
- **InvalidUuidException (400):** UUID inválido

## Validaciones
- Juego debe existir
- Nombre es requerido
- Fecha inicio < Fecha fin
- maxTeams > 0
- Fechas no pueden ser en el pasado
