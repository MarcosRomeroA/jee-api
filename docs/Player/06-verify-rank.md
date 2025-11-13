# Caso de Uso: Verificar Rango de Jugador

## Información del Endpoint
- **Ruta:** `/player/verify-rank`
- **Método HTTP:** `POST`
- **Autenticación requerida:** Sí

## Descripción
Permite verificar el rango de un jugador mediante integración con la API del juego (ej: Riot API).

## Request Body
```json
{
  "playerId": "uuid-del-jugador"
}
```

## Flujo Principal
1. Usuario solicita verificación de rango
2. Sistema obtiene datos de API externa del juego
3. Sistema compara rango declarado con rango real
4. Sistema actualiza estado de verificación
5. Sistema actualiza rango si es necesario

## Response Exitoso (200)
```json
{
  "verified": true,
  "currentRank": {
    "id": "uuid",
    "name": "Diamond II",
    "code": "DIAMOND_2"
  },
  "updated": true
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **PlayerNotFoundException (404):** Jugador no encontrado
- **RankVerificationException (400):** Error al verificar con API externa
- **GameNotFoundException (404):** Juego no soporta verificación

## Consideraciones
- Requiere integración con APIs externas (Riot, Steam, etc.)
- No todos los juegos soportan verificación automática
- Puede requerir credenciales adicionales del jugador
