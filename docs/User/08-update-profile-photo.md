# Caso de Uso: Actualizar Foto de Perfil

## Información del Endpoint
- **Ruta:** `/user-profile-image`
- **Método HTTP:** `POST`
- **Autenticación requerida:** Sí
- **Content-Type:** multipart/form-data

## Request
```
POST /user-profile-image
Content-Type: multipart/form-data

image: [archivo de imagen]
```

## Flujo Principal
1. Usuario autenticado sube imagen
2. Sistema valida tipo de archivo (jpg, png, etc.)
3. Sistema valida tamaño de imagen
4. Sistema procesa y optimiza imagen
5. Sistema guarda imagen en almacenamiento
6. Sistema actualiza URL de perfil del usuario

## Response Exitoso (200)
```json
{
  "imageUrl": "https://storage.../profile/user-id.jpg"
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **ImageUploadFailedException (500):** Error al subir imagen
- **ValidationException (400):** Archivo inválido o muy grande
- **UserNotFoundException (404):** Usuario no encontrado

## Validaciones
- Tipos permitidos: jpg, jpeg, png, gif
- Tamaño máximo: típicamente 5-10 MB
- Dimensiones recomendadas: mínimo 200x200px
