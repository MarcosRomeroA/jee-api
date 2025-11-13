# Caso de Uso: Obtener Usuarios Seguidos

## Información del Endpoint
- **Ruta:** `/user/{id}/followings`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Query Parameters
- `page`: Número de página
- `limit`: Resultados por página

## Flujo Principal
Obtiene lista de usuarios que el usuario especificado está siguiendo.

## Response Exitoso (200)
```json
{
  "followings": [
    {
      "id": "uuid",
      "username": "seguido1",
      "firstname": "Nombre",
      "lastname": "Apellido"
    }
  ],
  "pagination": {
    "page": 1,
    "total": 200
  }
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **UserNotFoundException (404):** Usuario no encontrado
