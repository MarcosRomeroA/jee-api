# Caso de Uso: Buscar Publicación

## Información del Endpoint
- **Ruta:** `/post/{id}`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Flujo Principal
Obtiene información detallada de una publicación.

## Response Exitoso (200)
```json
{
  "id": "uuid",
  "body": "Contenido",
  "author": {
    "id": "uuid",
    "username": "autor",
    "profileImage": "https://..."
  },
  "resources": [
    {
      "id": "uuid",
      "type": "image",
      "url": "https://..."
    }
  ],
  "likes": 150,
  "comments": 25,
  "likedByMe": false,
  "createdAt": "2024-01-15T10:30:00Z"
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **PostNotFoundException (404):** Publicación no encontrada
- **InvalidUuidException (400):** UUID inválido
