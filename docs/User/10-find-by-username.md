# Caso de Uso: Buscar Usuario por Username

## Información del Endpoint
- **Ruta:** `/user/by-username/{username}`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Flujo Principal
1. Usuario autenticado busca por username
2. Sistema busca usuario
3. Sistema devuelve información

## Response Exitoso (200)
```json
{
  "id": "uuid",
  "username": "juanperez",
  "firstname": "Juan",
  "lastname": "Pérez",
  "email": "juan@ejemplo.com",
  "profileImage": "https://..."
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **UserNotFoundException (404):** Username no encontrado

## Consideraciones
- Username es case-sensitive
