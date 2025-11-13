# Caso de Uso: Buscar Conversaciones Activas

## Información del Endpoint
- **Ruta:** `/conversations`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Query Parameters
- `page`: Página
- `limit`: Resultados por página

## Flujo Principal
Obtiene todas las conversaciones del usuario autenticado.

## Response Exitoso (200)
```json
{
  "conversations": [
    {
      "id": "uuid",
      "participants": [
        {
          "id": "uuid",
          "username": "usuario1",
          "profileImage": "https://..."
        },
        {
          "id": "uuid",
          "username": "usuario2",
          "profileImage": "https://..."
        }
      ],
      "lastMessage": {
        "id": "uuid",
        "content": "Último mensaje",
        "senderId": "uuid",
        "createdAt": "2024-01-15T10:30:00Z"
      },
      "unreadCount": 3,
      "updatedAt": "2024-01-15T10:30:00Z"
    }
  ],
  "pagination": {...}
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido

## Consideraciones
- Ordenado por última actividad (updatedAt descendente)
- Muestra contador de mensajes no leídos
