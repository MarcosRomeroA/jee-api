# Caso de Uso: Crear Mensaje

## Información del Endpoint
- **Ruta:** `/conversation/{conversationId}/message/{messageId}`
- **Método HTTP:** `PUT`
- **Autenticación requerida:** Sí

## Request Body
```json
{
  "content": "Contenido del mensaje",
  "receiverId": "uuid-del-destinatario"
}
```

## Flujo Principal
1. Usuario envía mensaje en conversación
2. Sistema valida que usuario es participante
3. Sistema valida que destinatario es participante
4. Sistema crea mensaje
5. Sistema actualiza última actividad de conversación
6. Sistema notifica al destinatario (si está online - Mercure)

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **ConversationNotFoundException (404):** Conversación no encontrada
- **UserNotExistsInConversationException (403):** Usuario no es participante
- **UserNotFoundException (404):** Destinatario no encontrado
- **InvalidUuidException (400):** UUID inválido
- **TextIsLongerThanAllowedException (400):** Mensaje muy largo

## Validaciones
- Contenido es requerido
- Máximo de caracteres (ej: 2000)
- Usuario debe ser participante
- Destinatario debe ser participante

## Consideraciones
- Si es el primer mensaje, se crea la conversación automáticamente
- Notificaciones en tiempo real vía Mercure
