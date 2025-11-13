# Caso de Uso: Crear Usuario (Registro)

## Información del Endpoint
- **Ruta:** `/user`
- **Método HTTP:** `POST`
- **Autenticación requerida:** No

## Descripción
Permite registrar un nuevo usuario en el sistema. El usuario debe proporcionar su información personal y credenciales.

## Request Body
```json
{
  "id": "uuid-generado-cliente",
  "firstname": "Juan",
  "lastname": "Pérez",
  "username": "juanperez",
  "email": "juan@ejemplo.com",
  "password": "MiPassword123!",
  "confirmationPassword": "MiPassword123!"
}
```

## Flujo Principal (Caso Exitoso)

1. El cliente genera un UUID para el nuevo usuario
2. El usuario proporciona todos sus datos
3. El sistema valida el formato del email
4. El sistema valida los requisitos de la contraseña
5. El sistema verifica que ambas contraseñas coincidan
6. El sistema verifica que el email no esté registrado
7. El sistema verifica que el username no esté registrado
8. El sistema hashea la contraseña usando BCRYPT
9. El sistema crea el usuario en la base de datos
10. El sistema envía un email de confirmación (evento asíncrono)
11. El sistema devuelve respuesta exitosa vacía

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

### 2. Email ya registrado
**Excepción:** `EmailAlreadyExistsException`
- **Cuándo ocurre:** Ya existe un usuario con ese email en el sistema
- **Código HTTP:** 409
- **Mensaje:** El email ya está registrado

### 3. Username ya registrado
**Excepción:** `UsernameAlreadyExistsException`
- **Cuándo ocurre:** Ya existe un usuario con ese username en el sistema
- **Código HTTP:** 409
- **Mensaje:** El nombre de usuario ya está en uso

### 4. Las contraseñas no coinciden
**Excepción:** `PasswordMismatchException`
- **Cuándo ocurre:** Los campos `password` y `confirmationPassword` no son iguales
- **Código HTTP:** 400
- **Mensaje:** Las contraseñas no coinciden

### 5. Contraseña sin longitud mínima
**Excepción:** `PasswordMinimumLengthRequiredException`
- **Cuándo ocurre:** La contraseña tiene 8 caracteres o menos
- **Código HTTP:** 400
- **Mensaje:** La contraseña debe tener más de 8 caracteres

### 6. Contraseña sin mayúsculas
**Excepción:** `PasswordUppercaseRequiredException`
- **Cuándo ocurre:** La contraseña no contiene al menos 1 letra mayúscula
- **Código HTTP:** 400
- **Mensaje:** La contraseña debe contener al menos 1 letra mayúscula

### 7. Contraseña sin caracteres especiales
**Excepción:** `PasswordSpecialCharacterRequiredException`
- **Cuándo ocurre:** La contraseña no contiene al menos 1 carácter especial
- **Código HTTP:** 400
- **Mensaje:** La contraseña debe contener al menos 1 carácter especial

### 8. UUID inválido
**Excepción:** `InvalidUuidException`
- **Cuándo ocurre:** El ID proporcionado no es un UUID válido
- **Código HTTP:** 400
- **Mensaje:** El UUID proporcionado no es válido

### 9. Campos requeridos faltantes
**Excepción:** `ValidationException`
- **Cuándo ocurre:** Falta algún campo requerido en el request
- **Código HTTP:** 400
- **Mensaje:** Campos requeridos faltantes

## Validaciones

1. **ID (UUID):**
   - Debe ser un UUID válido (formato UUID v4)
   - Campo requerido

2. **Firstname:**
   - Campo requerido
   - Debe ser string no vacío

3. **Lastname:**
   - Campo requerido
   - Debe ser string no vacío

4. **Username:**
   - Campo requerido
   - Debe ser único en el sistema
   - Debe ser string no vacío

5. **Email:**
   - Campo requerido
   - Debe tener formato válido
   - Debe ser único en el sistema

6. **Password:**
   - Campo requerido
   - Longitud mayor a 8 caracteres
   - Al menos 1 letra mayúscula
   - Al menos 1 carácter especial
   - Debe coincidir con confirmationPassword

7. **ConfirmationPassword:**
   - Campo requerido
   - Debe ser igual a password

## Eventos Generados

- **UserCreated:** Se dispara después de crear el usuario exitosamente
  - Desencadena el envío de email de confirmación (proceso asíncrono)

## Consideraciones

- El email de confirmación se envía de forma asíncrona a través de un sistema de mensajería (RabbitMQ)
- El usuario puede iniciar sesión antes de confirmar su email (dependiendo de la lógica implementada)
- La contraseña se almacena hasheada, nunca en texto plano
- El username es case-sensitive
- El email se almacena en minúsculas (dependiendo de la implementación)
