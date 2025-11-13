# Caso de Uso: Buscar Usuarios

## Información del Endpoint
- **Ruta:** `/users`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Query Parameters
- `q`: Texto de búsqueda (username, nombre, apellido)
- `page`: Número de página (default: 1)
- `limit`: Resultados por página (default: 20, max: 100)

## Ejemplo
```
GET /users?q=juan&page=1&limit=20
```

## Flujo Principal
1. Usuario autenticado busca otros usuarios
2. Sistema busca por username, firstname, lastname
3. Sistema pagina resultados
4. Sistema devuelve lista de usuarios

## Response Exitoso (200)
```json
{
  "users": [
    {
      "id": "uuid",
      "username": "juanperez",
      "firstname": "Juan",
      "lastname": "Pérez",
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

## Consideraciones
- Búsqueda case-insensitive
- Resultados ordenados por relevancia
