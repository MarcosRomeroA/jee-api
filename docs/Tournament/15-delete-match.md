# Caso de Uso: Eliminar Partido

## Información del Endpoint
- **Ruta:** `/match/{id}`
- **Método HTTP:** `DELETE`
- **Autenticación requerida:** Sí

## Flujo Principal
1. Organizador elimina partido
2. Sistema valida permisos
3. Sistema valida estado
4. Sistema elimina partido

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** No es organizador/responsable
- **MatchNotFoundException (404):** Partido no encontrado
- **InvalidMatchStateException (400):** Partido ya completado
