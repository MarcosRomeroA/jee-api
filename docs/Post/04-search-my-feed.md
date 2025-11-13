# Caso de Uso: Obtener Mi Feed

## Información del Endpoint
- **Ruta:** `/my-feed`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Query Parameters
- `page`: Página
- `limit`: Resultados por página

## Flujo Principal
Obtiene publicaciones de usuarios que el usuario autenticado sigue.

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

## Consideraciones
- Ordenado por fecha descendente
- Solo muestra posts de usuarios seguidos
