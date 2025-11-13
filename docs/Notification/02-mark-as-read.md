# Caso de Uso: Marcar Notificación como Leída

## Información del Endpoint
- **Ruta:** `/notification/{id}/mark-as-read`
- **Método HTTP:** `PUT`
- **Autenticación requerida:** Sí

## Flujo Principal
1. Usuario marca notificación como leída
2. Sistema valida que notificación pertenece al usuario
3. Sistema marca notificación como leída

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido/notificación de otro usuario
- **NotificationNotFoundException (404):** Notificación no encontrada
- **InvalidUuidException (400):** UUID inválido

## Consideraciones
- Operación idempotente: si ya estaba leída, no hace nada
- Usuario solo puede marcar sus propias notificaciones
