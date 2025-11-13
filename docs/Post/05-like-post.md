# Caso de Uso: Dar Like a Publicación

## Información del Endpoint
- **Ruta:** `/post/{id}/like`
- **Método HTTP:** `PUT`
- **Autenticación requerida:** Sí

## Flujo Principal
1. Usuario autenticado da like a publicación
2. Sistema valida que post existe
3. Sistema registra like
4. Sistema incrementa contador
5. Sistema notifica al autor

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **PostNotFoundException (404):** Publicación no encontrada

## Consideraciones
- Operación idempotente: si ya dio like, no hace nada
