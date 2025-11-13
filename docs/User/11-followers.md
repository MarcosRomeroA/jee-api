# Caso de Uso: Obtener Seguidores de Usuario

## Información del Endpoint
- **Ruta:** `/user/{id}/followers`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Query Parameters
- `page`: Número de página (default: 1)
- `limit`: Resultados por página (default: 20)

## Flujo Principal
1. Usuario solicita lista de seguidores de un usuario
2. Sistema obtiene seguidores paginados
3. Sistema devuelve lista

## Response Exitoso (200)
```json
{
  "followers": [
    {
      "id": "uuid",
      "username": "follower1",
      "firstname": "Nombre",
      "lastname": "Apellido",
      "profileImage": "https://..."
    }
  ],
  "pagination": {
    "page": 1,
    "limit": 20,
    "total": 150
  }
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **UserNotFoundException (404):** Usuario no encontrado
- **InvalidUuidException (400):** UUID inválido
