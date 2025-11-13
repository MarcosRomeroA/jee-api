# Caso de Uso: Actualizar Resultado del Partido

## Información del Endpoint
- **Ruta:** `/match/{id}/result`
- **Método HTTP:** `PUT`
- **Autenticación requerida:** Sí

## Request Body
```json
{
  "winnerId": "uuid-equipo-ganador",
  "team1Score": 2,
  "team2Score": 1
}
```

## Flujo Principal
1. Organizador actualiza resultado
2. Sistema valida permisos
3. Sistema valida que partido está IN_PROGRESS
4. Sistema registra resultado
5. Sistema marca partido como COMPLETED
6. Sistema notifica a equipos

## Response Exitoso (200)
```json
{}
```

## Excepciones
- **UnauthorizedException (401):** No es organizador/responsable
- **MatchNotFoundException (404):** Partido no encontrado
- **InvalidMatchStateException (400):** Partido no está IN_PROGRESS
- **TeamNotFoundException (404):** Ganador no es participante
