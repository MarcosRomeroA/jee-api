# Caso de Uso: Eliminar Equipo

## Información del Endpoint
- **Ruta:** `/team/{id}`
- **Método HTTP:** `DELETE`
- **Autenticación requerida:** Sí

## Flujo Principal
1. Usuario autenticado elimina equipo
2. Sistema valida que es dueño
3. Sistema verifica que no esté en torneos activos
4. Sistema elimina equipo

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido/no es dueño
- **TeamNotFoundException (404):** Equipo no encontrado
- **InvalidTournamentStateException (400):** Equipo en torneo activo
