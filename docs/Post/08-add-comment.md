# Caso de Uso: Agregar Comentario

## Información del Endpoint
- **Ruta:** `/post/{id}/comment`
- **Método HTTP:** `PUT`
- **Autenticación requerida:** Sí

## Request Body
```json
{
  "commentId": "uuid-generado",
  "content": "Mi comentario"
}
```

## Flujo Principal
1. Usuario comenta en publicación
2. Sistema valida contenido
3. Sistema crea comentario
4. Sistema notifica al autor del post

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **PostNotFoundException (404):** Publicación no encontrada
- **TextIsLongerThanAllowedException (400):** Comentario muy largo
