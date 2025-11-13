# Caso de Uso: Iniciar Partido

## Información del Endpoint
- **Ruta:** `/match/{id}/start`
- **Método HTTP:** `POST`
- **Autenticación requerida:** Sí

## Flujo Principal
1. Organizador inicia partido
2. Sistema valida permisos
3. Sistema valida estado del partido
4. Sistema marca partido como IN_PROGRESS

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** No es organizador/responsable
- **MatchNotFoundException (404):** Partido no encontrado
- **InvalidMatchStateException (400):** Partido ya iniciado/finalizado
