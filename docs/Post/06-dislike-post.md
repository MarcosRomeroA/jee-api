# Caso de Uso: Quitar Like a Publicación

## Información del Endpoint
- **Ruta:** `/post/{id}/dislike`
- **Método HTTP:** `PUT`
- **Autenticación requerida:** Sí

## Flujo Principal
1. Usuario autenticado quita like
2. Sistema elimina like
3. Sistema decrementa contador

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **PostNotFoundException (404):** Publicación no encontrada

## Consideraciones
- Operación idempotente
