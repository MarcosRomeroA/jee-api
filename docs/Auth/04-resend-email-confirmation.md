# Caso de Uso: Reenviar Email de Confirmación

## Información del Endpoint
- **Ruta:** `/auth/resend-confirmation`
- **Método HTTP:** `POST`
- **Autenticación requerida:** No

## Descripción
Permite reenviar el email de confirmación cuando el token original ha expirado o el usuario no lo recibió.

## Request Body
```json
{
  "email": "usuario@ejemplo.com"
}
```

## Flujo Principal (Caso Exitoso)

1. El usuario proporciona su email
2. El sistema valida el formato del email
3. El sistema busca el usuario por email
4. El sistema verifica que el email no esté ya confirmado
5. El sistema genera un nuevo token de confirmación
6. El sistema envía un nuevo email de confirmación (proceso asíncrono)
7. El sistema devuelve respuesta exitosa

### Response Exitoso (200)
```json
{}
```

## Flujos Alternativos y Excepciones

### 1. Email con formato inválido
**Excepción:** `InvalidEmailException`
- **Cuándo ocurre:** El email no cumple con el formato válido
- **Código HTTP:** 400
- **Mensaje:** El email no tiene un formato válido

### 2. Usuario no encontrado
**Excepción:** `UserNotFoundException`
- **Cuándo ocurre:** No existe un usuario registrado con ese email
- **Código HTTP:** 404
- **Mensaje:** Usuario no encontrado
- **Consideración de seguridad:** Puede revelar si un email está registrado

### 3. Email ya confirmado
**Excepción:** `EmailAlreadyConfirmedException`
- **Cuándo ocurre:** El usuario ya confirmó su email previamente
- **Código HTTP:** 409
- **Mensaje:** El email ya ha sido confirmado
- **Acción:** El usuario puede proceder a iniciar sesión directamente

## Validaciones

1. **Email:**
   - Campo requerido
   - Debe tener formato válido
   - Debe corresponder a un usuario registrado
   - El usuario no debe tener el email ya confirmado

## Comportamiento del Sistema

- **Invalidación del token anterior:** Si existe un token de confirmación anterior (expirado o no usado), el sistema puede:
  - Invalidarlo y generar uno nuevo
  - O simplemente generar uno adicional (dependiendo de la implementación)

- **Rate Limiting:** Es recomendable implementar límites de frecuencia para evitar:
  - Spam de emails
  - Ataques de enumeración de usuarios
  - Abuso del sistema de email

## Eventos Generados

- **EmailConfirmationResent:** Se dispara al generar un nuevo token
  - Desencadena el envío del email de confirmación (proceso asíncrono)

## Consideraciones de Seguridad

- **Enumeración de usuarios:** Este endpoint puede revelar si un email está registrado en el sistema
  - Solución alternativa: Devolver siempre éxito, independientemente de si el usuario existe
  
- **Rate Limiting:** Limitar la cantidad de solicitudes:
  - Por IP: Evitar abuso masivo
  - Por email: Evitar spam a un usuario específico
  - Ejemplo: Máximo 3 reenvíos por email cada 24 horas

- **Email asíncrono:** El envío se realiza a través de cola de mensajes (RabbitMQ) para:
  - No bloquear la respuesta al usuario
  - Garantizar reintentos en caso de fallo
  - Mejorar la experiencia del usuario

## Tiempo de Expiración

- Los nuevos tokens típicamente tienen el mismo tiempo de expiración que los originales (24-48 horas)
- El usuario puede solicitar múltiples reenvíos si el token expira nuevamente
