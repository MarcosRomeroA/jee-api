# Caso de Uso: Actualizar Equipo

## Información del Endpoint
- **Ruta:** `/team/{id}`
- **Método HTTP:** `PUT`
- **Autenticación requerida:** Sí

## Request Body
```json
{
  "name": "Nuevo Nombre",
  "image": "https://nueva-imagen.jpg"
}
```

## Flujo Principal
1. Usuario autenticado actualiza equipo
2. Sistema valida que el usuario es dueño del equipo
3. Sistema actualiza información

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido/no es dueño
- **TeamNotFoundException (404):** Equipo no encontrado
- **InvalidUuidException (400):** UUID inválido
