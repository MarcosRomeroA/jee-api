# Caso de Uso: Eliminar Publicación

## Información del Endpoint
- **Ruta:** `/post/{id}/delete`
- **Método HTTP:** `DELETE`
- **Autenticación requerida:** Sí

## Flujo Principal
1. Usuario autenticado elimina su publicación
2. Sistema valida que usuario es el autor
3. Sistema elimina publicación

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **PostNotFoundException (404):** Publicación no encontrada
- **PostDeletionNotAllowedException (403):** Usuario no es el autor
