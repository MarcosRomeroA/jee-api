# Caso de Uso: Buscar Jugador

## Información del Endpoint
- **Ruta:** `/player/{id}`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Flujo Principal
1. Usuario busca perfil de jugador
2. Sistema obtiene información del jugador
3. Sistema devuelve datos

## Response Exitoso (200)
```json
{
  "id": "uuid",
  "username": "jugador123",
  "game": {
    "id": "uuid",
    "name": "League of Legends"
  },
  "rank": {
    "id": "uuid",
    "name": "Diamond",
    "code": "DIAMOND"
  },
  "role": {
    "id": "uuid",
    "name": "ADC"
  },
  "verified": false
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **PlayerNotFoundException (404):** Jugador no encontrado
- **InvalidUuidException (400):** UUID inválido
