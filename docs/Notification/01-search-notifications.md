# Caso de Uso: Buscar Notificaciones

## Información del Endpoint
- **Ruta:** `/notifications`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Query Parameters
- `read`: Filtrar por leídas/no leídas (true/false)
- `page`: Página
- `limit`: Resultados por página

## Flujo Principal
Obtiene notificaciones del usuario autenticado.

## Response Exitoso (200)
```json
{
  "notifications": [
    {
      "id": "uuid",
      "type": "USER_FOLLOWED",
      "message": "usuario1 comenzó a seguirte",
      "read": false,
      "data": {
        "userId": "uuid",
        "username": "usuario1"
      },
      "createdAt": "2024-01-15T10:30:00Z"
    },
    {
      "id": "uuid",
      "type": "POST_LIKED",
      "message": "A usuario2 le gustó tu publicación",
      "read": true,
      "data": {
        "postId": "uuid",
        "userId": "uuid"
      },
      "createdAt": "2024-01-15T09:00:00Z"
    }
  ],
  "pagination": {
    "page": 1,
    "total": 50,
    "unreadCount": 10
  }
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido

## Tipos de Notificaciones
- USER_FOLLOWED: Nuevo seguidor
- POST_LIKED: Like en publicación
- POST_COMMENTED: Comentario en publicación
- TEAM_REQUEST: Solicitud para unirse a equipo
- TEAM_REQUEST_ACCEPTED: Solicitud aceptada
- TOURNAMENT_TEAM_ADDED: Equipo agregado a torneo
- MATCH_SCHEDULED: Partido programado
- MATCH_RESULT: Resultado de partido
