# Caso de Uso: Solicitar Acceso a Equipo

## Información del Endpoint
- **Ruta:** `/team/{teamId}/request-access`
- **Método HTTP:** `POST`
- **Autenticación requerida:** Sí

## Flujo Principal
1. Usuario solicita unirse a equipo
2. Sistema valida que equipo existe
3. Sistema valida que usuario no es miembro
4. Sistema crea solicitud
5. Sistema notifica al dueño del equipo

## Response Exitoso (200)
```json
{
  "requestId": "uuid"
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **TeamNotFoundException (404):** Equipo no encontrado
- **RequestAlreadyExistsException (409):** Ya existe solicitud pendiente
- **PlayerNotFoundException (404):** Usuario debe tener perfil de jugador del juego
