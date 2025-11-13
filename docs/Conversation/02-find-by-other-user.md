# Caso de Uso: Buscar Conversación con Usuario

## Información del Endpoint
- **Ruta:** `/conversation/by-other-user/{id}`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Flujo Principal
1. Usuario busca conversación con otro usuario específico
2. Sistema busca conversación entre ambos usuarios
3. Sistema devuelve conversación si existe

## Response Exitoso (200)
```json
{
  "id": "uuid",
  "participants": [
    {
      "id": "uuid",
      "username": "usuario1"
    },
    {
      "id": "uuid",
      "username": "usuario2"
    }
  ],
  "createdAt": "2024-01-10T10:00:00Z"
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **ConversationNotFoundException (404):** No existe conversación entre ambos usuarios
- **InvalidUuidException (400):** UUID inválido
- **UserNotFoundException (404):** Usuario no encontrado

## Consideraciones
- Si no existe conversación, retorna 404
- El cliente puede crear una nueva conversación enviando el primer mensaje
