# Caso de Uso: Eliminar Perfil de Jugador

## Información del Endpoint
- **Ruta:** `/player/{id}`
- **Método HTTP:** `DELETE`
- **Autenticación requerida:** Sí

## Flujo Principal
1. Usuario autenticado elimina su perfil de jugador
2. Sistema valida que el jugador pertenece al usuario
3. Sistema elimina perfil (soft delete o hard delete)

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido/no es dueño
- **PlayerNotFoundException (404):** Jugador no encontrado
- **InvalidUuidException (400):** UUID inválido

## Consideraciones
- Puede ser eliminación lógica (soft delete)
- Si el jugador está en equipos, puede requerir salir primero
