# Caso de Uso: Agregar Recurso Temporal

## Información del Endpoint
- **Ruta:** `/post/{id}/resource`
- **Método HTTP:** `POST`
- **Autenticación requerida:** Sí
- **Content-Type:** multipart/form-data

## Request
```
POST /post/{id}/resource
Content-Type: multipart/form-data

file: [archivo]
type: "image"
```

## Flujo Principal
1. Usuario sube recurso (imagen/video) antes de crear post
2. Sistema valida archivo
3. Sistema guarda recurso temporalmente
4. Sistema devuelve ID del recurso
5. Usuario usa ID al crear post

## Response Exitoso (200)
```json
{
  "resourceId": "uuid",
  "url": "https://storage.../temp/resource.jpg"
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **ValidationException (400):** Archivo inválido
- **ImageUploadFailedException (500):** Error al subir

## Consideraciones
- Recursos temporales expiran si no se usan (ej: 24h)
- Tipos permitidos: imagen, video
- Tamaño máximo definido por tipo
