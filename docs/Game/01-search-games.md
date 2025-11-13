# Caso de Uso: Buscar Juegos

## Información del Endpoint
- **Ruta:** `/games`
- **Método HTTP:** `GET`
- **Autenticación requerida:** Sí

## Query Parameters
- `q`: Texto de búsqueda (nombre)
- `page`: Página
- `limit`: Resultados por página

## Flujo Principal
Búsqueda de juegos disponibles en la plataforma.

## Response Exitoso (200)
```json
{
  "games": [
    {
      "id": "uuid",
      "name": "League of Legends",
      "description": "MOBA 5v5",
      "image": "https://...",
      "ranks": [
        {
          "id": "uuid",
          "name": "Iron",
          "code": "IRON"
        }
      ],
      "roles": [
        {
          "id": "uuid",
          "name": "ADC"
        }
      ]
    }
  ],
  "pagination": {...}
}
```

## Excepciones
- **UnauthorizedException (401):** Token inválido

## Consideraciones
- Los juegos son gestionados por administradores
- Cada juego tiene rangos y roles específicos
