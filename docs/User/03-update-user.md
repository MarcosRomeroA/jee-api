# Caso de Uso: Actualizar Usuario

## Información del Endpoint
- **Ruta:** `/user`
- **Método HTTP:** `PUT`
- **Autenticación requerida:** Sí (JWT Token)

## Descripción
Permite a un usuario autenticado actualizar su propia información personal.

## Request Body
```json
{
  "firstname": "Juan",
  "lastname": "Pérez García",
  "username": "juanperez_nuevo",
  "email": "nuevo@ejemplo.com"
}
```

## Headers Requeridos
```
Authorization: Bearer {jwt-token}
```

## Flujo Principal (Caso Exitoso)

1. El usuario autenticado envía sus datos actualizados
2. El sistema valida el token JWT y extrae el ID del usuario
3. El sistema valida el formato del email
4. El sistema verifica que el nuevo username no esté en uso por otro usuario
5. El sistema verifica que el nuevo email no esté en uso por otro usuario
6. El sistema actualiza la información del usuario
7. El sistema guarda los cambios
8. El sistema devuelve respuesta exitosa

### Response Exitoso (200)
```json
{}
```

## Flujos Alternativos y Excepciones

### 1. Usuario no autenticado
**Excepción:** `UnauthorizedException`
- **Cuándo ocurre:** Token inválido, expirado o no proporcionado
- **Código HTTP:** 401
- **Mensaje:** No autorizado

### 2. Email con formato inválido
**Excepción:** `InvalidEmailException`
- **Cuándo ocurre:** El nuevo email no cumple con el formato válido
- **Código HTTP:** 400
- **Mensaje:** El email no tiene un formato válido

### 3. Email ya en uso
**Excepción:** `EmailAlreadyExistsException`
- **Cuándo ocurre:** Otro usuario ya tiene registrado ese email
- **Código HTTP:** 409
- **Mensaje:** El email ya está registrado

### 4. Username ya en uso
**Excepción:** `UsernameAlreadyExistsException`
- **Cuándo ocurre:** Otro usuario ya tiene ese username
- **Código HTTP:** 409
- **Mensaje:** El nombre de usuario ya está en uso

### 5. Usuario no encontrado
**Excepción:** `UserNotFoundException`
- **Cuándo ocurre:** El usuario autenticado no existe en la base de datos (caso excepcional)
- **Código HTTP:** 404
- **Mensaje:** Usuario no encontrado

## Validaciones

1. **Token JWT:**
   - Debe ser válido y no expirado
   - Campo requerido

2. **Firstname:**
   - Campo requerido
   - No debe estar vacío

3. **Lastname:**
   - Campo requerido
   - No debe estar vacío

4. **Username:**
   - Campo requerido
   - Debe ser único (si cambió)
   - No debe estar vacío

5. **Email:**
   - Campo requerido
   - Formato válido
   - Debe ser único (si cambió)

## Consideraciones

- El usuario solo puede actualizar su propia información (determinada por el JWT)
- Si el email cambia, puede ser necesario reconfirmar el nuevo email (dependiendo de la implementación)
- El username es case-sensitive
- No se puede cambiar el ID del usuario
- No se puede cambiar la contraseña mediante este endpoint (existe un endpoint específico)
