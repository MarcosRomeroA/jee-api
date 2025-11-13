# Caso de Uso: Buscar Juego por ID

## Información del Endpoint
- **Ruta:** `/game/{id}`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Flujo Principal
Obtiene información detallada de un juego específico.

## Response Exitoso (200)
```json
{
  "id": "uuid",
  "name": "League of Legends",
  "description": "Multiplayer Online Battle Arena",
  "image": "https://...",
  "ranks": [
    {
      "id": "uuid",
      "name": "Iron",
      "code": "IRON",
      "order": 1
    },
    {
      "id": "uuid",
      "name": "Bronze",
      "code": "BRONZE",
      "order": 2
    }
  ],
  "roles": [
    {
      "id": "uuid",
      "name": "Top"
    },
    {
      "id": "uuid",
      "name": "Jungle"
    },
    {
      "id": "uuid",
      "name": "Mid"
    },
    {
      "id": "uuid",
      "name": "ADC"
    },
    {
      "id": "uuid",
      "name": "Support"
    }
  ]
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido
- **GameNotFoundException (404):** Juego no encontrado
- **InvalidUuidException (400):** UUID inválido

## Consideraciones
- Los rangos tienen un orden jerárquico
- Los roles son específicos de cada juego
