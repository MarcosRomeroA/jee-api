# Caso de Uso: Aceptar Solicitud de Acceso

## Información del Endpoint
- **Ruta:** `/team/request/{requestId}/accept`
- **Método HTTP:** `POST`
- **Autenticación requerida:** Sí

## Flujo Principal
1. Dueño del equipo acepta solicitud
2. Sistema valida que usuario es dueño
3. Sistema agrega solicitante como miembro
4. Sistema marca solicitud como aceptada
5. Sistema notifica al solicitante

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido/no es dueño
- **RequestNotFoundException (404):** Solicitud no encontrada
- **TeamNotFoundException (404):** Equipo no encontrado
- **UserNotFoundException (404):** Solicitante no encontrado
