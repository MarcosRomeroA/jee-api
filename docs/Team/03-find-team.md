# Caso de Uso: Buscar Equipo

## Información del Endpoint
- **Ruta:** `/team/{id}`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Flujo Principal
Obtiene información detallada del equipo.

## Response Exitoso (200)
```json
{
  "id": "uuid",
  "name": "Team Awesome",
  "image": "https://...",
  "game": {
    "id": "uuid",
    "name": "League of Legends"
  },
  "owner": {
    "id": "uuid",
    "username": "owner"
  },
  "members": [
    {
      "id": "uuid",
      "username": "member1",
      "role": "player"
    }
  ],
  "createdAt": "2024-01-15T10:30:00Z"
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **TeamNotFoundException (404):** Equipo no encontrado
- **InvalidUuidException (400):** UUID inválido
