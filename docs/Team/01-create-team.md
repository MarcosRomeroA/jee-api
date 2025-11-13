# Caso de Uso: Crear Equipo

## Información del Endpoint
- **Ruta:** `/team`
- **Método HTTP:** `POST`
- **Autenticación requerida:** Sí

## Request Body
```json
{
  "id": "uuid-generado",
  "gameId": "uuid-del-juego",
  "name": "Team Awesome",
  "image": "https://imagen-del-equipo.jpg"
}
```

## Flujo Principal
1. Usuario autenticado crea equipo
2. Sistema valida que el juego existe
3. Sistema valida que el usuario existe
4. Sistema crea equipo con usuario como dueño
5. Sistema agrega al usuario como primer miembro

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **GameNotFoundException (404):** Juego no encontrado
- **UserNotFoundException (404):** Usuario no encontrado
- **InvalidUuidException (400):** UUID inválido
- **ValidationException (400):** Nombre requerido

## Validaciones
- Juego debe existir
- Nombre es requerido
- Imagen es opcional
