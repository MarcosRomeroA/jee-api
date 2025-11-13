# Caso de Uso: Buscar Mis Equipos

## Información del Endpoint
- **Ruta:** `/my-teams`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Flujo Principal
Obtiene equipos donde el usuario es miembro o dueño.

## Response Exitoso (200)
```json
{
  "teams": [
    {
      "id": "uuid",
      "name": "Mi Equipo",
      "role": "owner",
      "game": {...}
    }
  ]
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
