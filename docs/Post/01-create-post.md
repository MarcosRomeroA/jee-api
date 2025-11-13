# Caso de Uso: Crear Publicación

## Información del Endpoint
- **Ruta:** `/post/{id}`
- **Método HTTP:** `PUT`
- **Autenticación requerida:** Sí

## Request Body
```json
{
  "body": "Contenido de la publicación",
  "resources": [
    {
      "id": "uuid-recurso-1",
      "type": "image",
      "url": "https://..."
    }
  ]
}
```

## Flujo Principal
1. Usuario autenticado crea publicación
2. Sistema valida contenido
3. Sistema asocia recursos temporales subidos previamente
4. Sistema crea publicación
5. Sistema notifica a seguidores

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **PostAlreadyExistsException (409):** Ya existe post con ese ID
- **TextIsLongerThanAllowedException (400):** Contenido muy largo
- **ValidationException (400):** Datos inválidos

## Validaciones
- Contenido es requerido
- Máximo de caracteres (ej: 5000)
- Recursos deben existir y pertenecer al usuario
