# Caso de Uso: Buscar Publicaciones

## Información del Endpoint
- **Ruta:** `/posts`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Query Parameters
- `userId`: Filtrar por autor
- `page`: Página
- `limit`: Resultados por página

## Flujo Principal
Búsqueda de publicaciones, típicamente de un usuario específico.

## Response Exitoso (200)
```json
{
  "posts": [
    {
      "id": "uuid",
      "body": "Contenido",
      "author": {...},
      "likes": 150,
      "comments": 25,
      "createdAt": "2024-01-15T10:30:00Z"
    }
  ],
  "pagination": {...}
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
