# Caso de Uso: Buscar Mensajes de Conversación

## Información del Endpoint
- **Ruta:** `/conversation/{conversationId}/messages`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Query Parameters
- `page`: Página
- `limit`: Resultados por página (default: 50)
- `before`: Obtener mensajes antes de esta fecha (para scroll infinito)

## Flujo Principal
1. Usuario solicita mensajes de conversación
2. Sistema valida que usuario es participante
3. Sistema obtiene mensajes paginados
4. Sistema marca mensajes como leídos

## Response Exitoso (200)
```json
{
  "messages": [
    {
      "id": "uuid",
      "content": "Hola, ¿cómo estás?",
      "sender": {
        "id": "uuid",
        "username": "usuario1",
        "profileImage": "https://..."
      },
      "read": true,
      "createdAt": "2024-01-15T10:30:00Z"
    },
    {
      "id": "uuid",
      "content": "Bien, gracias",
      "sender": {
        "id": "uuid",
        "username": "usuario2"
      },
      "read": false,
      "createdAt": "2024-01-15T10:31:00Z"
    }
  ],
  "pagination": {
    "page": 1,
    "hasMore": true
  }
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **ConversationNotFoundException (404):** Conversación no encontrada
- **UserNotExistsInConversationException (403):** Usuario no es participante
- **InvalidUuidException (400):** UUID inválido

## Consideraciones
- Ordenado por fecha ascendente (más antiguos primero)
- Mensajes se marcan como leídos automáticamente
- Soporta scroll infinito con parámetro `before`
