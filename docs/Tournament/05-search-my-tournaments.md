# Caso de Uso: Buscar Mis Torneos

## Información del Endpoint
- **Ruta:** `/my-tournaments`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Flujo Principal
Obtiene torneos donde el usuario es organizador, responsable, o tiene equipo registrado.

## Response Exitoso (200)
```json
{
  "tournaments": [
    {
      "id": "uuid",
      "name": "Mi Torneo",
      "role": "organizer",
      "game": {...},
      "status": "IN_PROGRESS"
    }
  ]
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
