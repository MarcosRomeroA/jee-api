# Caso de Uso: Eliminar Torneo

## Información del Endpoint
- **Ruta:** `/tournament/{id}`
- **Método HTTP:** `DELETE`
- **Autenticación requerida:** Sí

## Flujo Principal
1. Usuario autenticado elimina torneo
2. Sistema valida que es organizador
3. Sistema valida estado del torneo
4. Sistema elimina torneo

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** No es organizador
- **TournamentNotFoundException (404):** Torneo no encontrado
- **InvalidTournamentStateException (400):** Torneo ya iniciado/finalizado

## Consideraciones
- Solo se puede eliminar si está en estado DRAFT u OPEN
- No se puede eliminar si ya inició
