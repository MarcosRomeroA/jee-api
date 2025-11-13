# Caso de Uso: Buscar Comentarios de Publicación

## Información del Endpoint
- **Ruta:** `/post/{id}/comments`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Query Parameters
- `page`: Página
- `limit`: Resultados por página

## Flujo Principal
Obtiene comentarios de una publicación.

## Response Exitoso (200)
```json
{
  "comments": [
    {
      "id": "uuid",
      "content": "Comentario",
      "author": {
        "id": "uuid",
        "username": "autor"
      },
      "createdAt": "2024-01-15T10:30:00Z"
    }
  ],
  "pagination": {...}
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **PostNotFoundException (404):** Publicación no encontrada
