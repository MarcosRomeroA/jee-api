# Convenciones de Testing

 

## Tabla de Contenidos

 

- [Objetivo](#objetivo)

- [Frameworks de Testing](#frameworks-de-testing)

- [Estructura de Directorios](#estructura-de-directorios)

- [Tipos de Tests](#tipos-de-tests)

- [Convenciones de Código](#convenciones-de-código)

- [Cómo Ejecutar los Tests](#cómo-ejecutar-los-tests)

- [Configuración de Cobertura](#configuración-de-cobertura)

- [Buenas Prácticas](#buenas-prácticas)

- [Ejemplos](#ejemplos)

 

---

 

## Objetivo

 

El objetivo del testing en este proyecto es garantizar la calidad, estabilidad y mantenibilidad del código a través de:

 

1. **Verificación de comportamiento**: Asegurar que cada componente funciona según lo esperado

2. **Prevención de regresiones**: Detectar errores introducidos por nuevos cambios

3. **Documentación viva**: Los tests sirven como documentación ejecutable del comportamiento del sistema

4. **Facilitar refactorización**: Permitir cambios en el código con confianza

5. **Validación de casos de uso**: Verificar que las funcionalidades cumplen los requisitos de negocio

 

El proyecto sigue una arquitectura hexagonal (DDD) y mantiene una clara separación entre:

- **Tests unitarios** (PHPUnit): Verifican componentes individuales en aislamiento

- **Tests de integración/BDD** (Behat): Validan flujos completos de negocio

 

---

 

## Frameworks de Testing

 

### PHPUnit 9.5

Framework de testing unitario para PHP que permite:

- Tests de unidad con mocks y stubs

- Assertions detalladas

- Generación de reportes de cobertura

- Integración con Symfony PHPUnit Bridge

 

### Behat 3.14

Framework de BDD (Behavior-Driven Development) que permite:

- Escribir tests en lenguaje natural (Gherkin)

- Tests de integración de APIs

- Validación de casos de uso de negocio

- Colaboración entre técnicos y no técnicos

 

**Extensiones de Behat:**

- **FriendsOfBehat/SymfonyExtension**: Integración con Symfony

- **FriendsOfBehat/MinkExtension**: Testing HTTP/API con BrowserKit

 

---

 

## Estructura de Directorios

 

```

tests/

├── bootstrap.php                    # Bootstrap de tests

├── Behat/                          # Tests de integración BDD

│   ├── Shared/

│   │   ├── Infrastructure/

│   │   │   ├── Behat/

│   │   │   │   ├── DatabaseContext.php    # Setup/teardown de BD

│   │   │   │   └── ApiContext.php         # Steps comunes de API

│   │   │   └── Mink/                      # Helpers HTTP

│   │   └── Fixtures/

│   │       └── TestUsers.php              # Datos de prueba compartidos

│   └── Web/                        # Features por módulo

│       ├── Auth/                   # Tests de autenticación

│       ├── Team/                   # Tests de equipos

│       ├── Player/                 # Tests de jugadores

│       ├── Post/                   # Tests de publicaciones

│       ├── Game/                   # Tests de juegos

│       ├── Tournament/             # Tests de torneos

│       ├── Notification/           # Tests de notificaciones

│       ├── Conversation/           # Tests de conversaciones

│       └── HealthCheck/            # Tests de health check

└── Unit/                           # Tests unitarios PHPUnit

    ├── Shared/

    │   └── Domain/ValueObject/     # Tests de value objects compartidos

    └── Web/

        ├── Team/                   # Tests unitarios de equipos

        ├── Game/                   # Tests unitarios de juegos

        ├── Tournament/             # Tests unitarios de torneos

        ├── Player/                 # Tests unitarios de jugadores

        ├── Post/                   # Tests unitarios de publicaciones

        └── User/                   # Tests unitarios de usuarios

```

 

**Estadísticas del proyecto:**

- 24 archivos de tests unitarios (PHPUnit)

- 47 archivos de features (Behat)

- 13 archivos de contextos (Behat)

 

**Organización:**

- Los tests reflejan la estructura del código fuente (`src/`)

- Cada módulo de negocio tiene sus propios tests unitarios y de integración

- Separación clara entre tests unitarios (`tests/Unit/`) y de integración (`tests/Behat/`)

 

---

 

## Tipos de Tests

 

### 1. Tests Unitarios (PHPUnit)

 

**Propósito**: Verificar el comportamiento de componentes individuales en aislamiento.

 

**Alcance**:

- Value Objects (validación de reglas de dominio)

- Servicios de aplicación (use cases)

- Repositorios (lógica de persistencia)

- Transformadores de datos

 

**Características**:

- Rápidos de ejecutar

- Sin dependencias externas (base de datos, APIs, etc.)

- Usan mocks y stubs para aislar dependencias

- Siguen el patrón AAA (Arrange, Act, Assert)

 

### 2. Tests de Integración/BDD (Behat)

 

**Propósito**: Validar flujos completos de negocio desde la perspectiva del usuario.

 

**Alcance**:

- APIs REST completas

- Autenticación y autorización

- Flujos de negocio end-to-end

- Interacción entre múltiples componentes

 

**Características**:

- Escritos en lenguaje natural (Gherkin)

- Usan base de datos de testing real

- Validan respuestas HTTP completas

- Verifican integración entre capas

 

---

 

## Convenciones de Código

 

### Tests Unitarios (PHPUnit)

 

#### Nomenclatura de Archivos

- **Archivo de test**: `{ClassName}Test.php`

- **Archivo de Object Mother**: `{ClassName}Mother.php`

 

**Ejemplo**:

```

TeamCreatorTest.php      # Test de TeamCreator

TeamMother.php          # Factory de datos de Team

```

 

#### Nomenclatura de Métodos

 

Dos estilos aceptados:

 

**Estilo 1: camelCase con prefijo `test`**

```php

public function testItShouldCreateATeam(): void

public function testItShouldThrowExceptionWhenNameIsEmpty(): void

```

 

**Estilo 2: snake_case con anotación `@test`**

```php

/** @test */

public function it_should_create_a_team(): void

 

/** @test */

public function it_should_throw_exception_when_name_is_empty(): void

```

 

**Convención de nombres**:

- Descriptivos y legibles

- Indican el comportamiento esperado

- Formato: `it_should_{expected_behavior}_when_{condition}`

 

#### Estructura de Clases de Test

 

```php

<?php

 

declare(strict_types=1);

 

namespace Tests\Unit\Web\Team\Application;

 

use PHPUnit\Framework\MockObject\MockObject;

use PHPUnit\Framework\TestCase;

use App\Web\Team\Application\TeamCreator;

use App\Web\Team\Domain\TeamRepository;

 

final class TeamCreatorTest extends TestCase

{

    private TeamRepository|MockObject $teamRepository;

    private TeamCreator $teamCreator;

 

    protected function setUp(): void

    {

        // Inicializar mocks y dependencias

        $this->teamRepository = $this->createMock(TeamRepository::class);

        $this->teamCreator = new TeamCreator($this->teamRepository);

    }

 

    public function testItShouldCreateATeam(): void

    {

        // Arrange: Preparar datos y comportamiento esperado

        $team = TeamMother::create();

        $this->teamRepository

            ->expects($this->once())

            ->method('save')

            ->with($team);

 

        // Act: Ejecutar la acción

        $this->teamCreator->create($team);

 

        // Assert: Verificar resultados (implícito en expects)

    }

}

```

 

#### Object Mother Pattern

 

Patrón utilizado extensivamente para la creación de datos de prueba:

 

```php

<?php

 

declare(strict_types=1);

 

namespace Tests\Unit\Web\Team\Domain;

 

use App\Web\Team\Domain\Team;

use App\Web\Team\Domain\TeamId;

use App\Web\Team\Domain\TeamName;

 

final class TeamMother

{

    public static function create(

        ?TeamId $id = null,

        ?TeamName $name = null,

        ?string $description = null

    ): Team {

        return new Team(

            $id ?? TeamIdMother::create(),

            $name ?? TeamNameMother::create(),

            $description ?? 'Default description'

        );

    }

 

    public static function random(): Team

    {

        return self::create(

            TeamIdMother::random(),

            TeamNameMother::random()

        );

    }

 

    public static function withName(string $name): Team

    {

        return self::create(

            name: TeamNameMother::create($name)

        );

    }

}

```

 

**Ventajas del Object Mother**:

- Datos de prueba consistentes

- Facilita la lectura de tests

- Reduce duplicación de código

- Permite crear variaciones fácilmente

 

#### Convenciones Generales

 

- ✅ Usar `declare(strict_types=1)` en todos los archivos

- ✅ Clases de test marcadas como `final`

- ✅ Usar type hints estrictos

- ✅ Usar mocks de PHPUnit para dependencias externas

- ✅ Un assert por test cuando sea posible

- ✅ Tests independientes entre sí

- ✅ Nombres de test descriptivos en inglés

 

### Tests de Integración (Behat)

 

#### Estructura de Features

 

```gherkin

@team @auth                                    # Tags para filtrar tests

Feature: Create Team

  In order to form gaming teams                # Valor de negocio

  As an authenticated user                     # Rol del usuario

  I want to create a new team                  # Objetivo de la funcionalidad

 

  Background:                                  # Opcional: pasos comunes

    Given the database is clean

 

  Scenario: Successfully create a team         # Escenario de éxito

    Given I am authenticated as "test@example.com" with password "password123"

    When I send a PUT request to "/api/team/550e8400-e29b-41d4-a716-446655440000" with body:

      """

      {

        "name": "Best Team",

        "description": "The best gaming team"

      }

      """

    Then the response status code should be 200

    And the response should have "id" property

    And the JSON node "name" should be equal to "Best Team"

 

  Scenario: Fail to create team without authentication  # Escenario de error

    Given I am not authenticated

    When I send a PUT request to "/api/team/550e8400-e29b-41d4-a716-446655440000" with body:

      """

      {

        "name": "Best Team"

      }

      """

    Then the response status code should be 401

```

 

#### Tags de Features

 

Los tags se usan para categorizar y filtrar tests:

 

- `@auth`: Tests de autenticación

- `@team`: Tests de equipos

- `@player`: Tests de jugadores

- `@game`: Tests de juegos

- `@tournament`: Tests de torneos

- `@post`: Tests de publicaciones

- `@notification`: Tests de notificaciones

- `@mercure`: Tests de integración con Mercure

 

**Uso de tags**:

```bash

# Ejecutar solo tests de equipos

make behat tag=team

 

# Ejecutar solo tests de autenticación

vendor/bin/behat --tags=@auth

```

 

#### Steps Comunes (ApiContext)

 

**Autenticación**:

```gherkin

Given I am authenticated as "user@example.com" with password "password123"

Given I am not authenticated

```

 

**Peticiones HTTP**:

```gherkin

When I send a GET request to "/api/teams"

When I send a POST request to "/api/teams" with body:

  """

  {"name": "Team Name"}

  """

When I send a PUT request to "/api/team/{uuid}"

When I send a DELETE request to "/api/team/{uuid}"

```

 

**Verificaciones de Respuesta**:

```gherkin

Then the response status code should be 200

Then the response status code should be 404

Then the response should be empty

Then the response should have "id" property

Then the response should contain pagination structure

Then the JSON node "name" should be equal to "Team Name"

Then the JSON node "items" should have 5 elements

```

 

#### Estructura de Contextos

 

```php

<?php

 

declare(strict_types=1);

 

namespace Tests\Behat\Web\Team;

 

use Behat\Behat\Context\Context;

use Tests\Behat\Shared\Infrastructure\Behat\ApiContext;

 

final class TeamTestContext implements Context

{

    public function __construct(

        private readonly ApiContext $apiContext

    ) {}

 

    /**

     * @BeforeScenario @team

     */

    public function setUp(): void

    {

        // Preparar datos específicos para tests de equipos

    }

 

    /**

     * @AfterScenario @team

     */

    public function tearDown(): void

    {

        // Limpiar datos después de cada escenario

    }

 

    /**

     * @Given there is a team with name :name

     */

    public function thereIsATeamWithName(string $name): void

    {

        // Implementación del step personalizado

    }

}

```

 

#### Convenciones de Gherkin

 

- ✅ Features en inglés para consistencia

- ✅ Usar tags para categorizar features

- ✅ Incluir descripción de valor de negocio (In order to...)

- ✅ Escenarios descriptivos y autoexplicativos

- ✅ Usar `Background` para pasos comunes

- ✅ Un concepto por escenario

- ✅ Evitar detalles técnicos en los steps cuando sea posible

 

---

 

## Cómo Ejecutar los Tests

 

### Usando Makefile (Recomendado)

 

El proyecto incluye un `Makefile` con comandos predefinidos para ejecutar tests en el entorno Docker:

 

#### Ejecutar Todos los Tests

```bash

make test

```

 

Ejecuta todos los tests unitarios (PHPUnit) y de integración (Behat).

 

#### Solo Tests Unitarios

```bash

make unit

```

 

Ejecuta únicamente los tests de PHPUnit.

 

#### Solo Tests de Integración

```bash

make behat

```

 

Ejecuta todos los tests de Behat.

 

#### Tests con Tags Específicos

```bash

make behat tag=team          # Solo tests de equipos

make behat tag=auth          # Solo tests de autenticación

make behat tag=mercure       # Solo tests de Mercure

```

 

#### Tests de Módulos Específicos

```bash

make test-player            # Tests del módulo de jugadores

make test-team              # Tests del módulo de equipos

```

 

#### Limpiar y Ejecutar Tests

```bash

make test-clean

```

 

Resetea la base de datos de testing y ejecuta todos los tests.

 

### Usando Composer/Binarios Directamente

 

#### PHPUnit

```bash

# Todos los tests unitarios

vendor/bin/phpunit

 

# Test específico

vendor/bin/phpunit tests/Unit/Web/Team/Application/TeamCreatorTest.php

 

# Con filtro de método

vendor/bin/phpunit --filter testItShouldCreateATeam

```

 

#### Behat

```bash

# Todos los features

vendor/bin/behat

 

# Feature específico

vendor/bin/behat tests/Behat/Web/Team/team_create.feature

 

# Con tags

vendor/bin/behat --tags=@team

vendor/bin/behat --tags=@auth

 

# Múltiples tags (AND)

vendor/bin/behat --tags=@team --tags=@auth

 

# Tags (OR)

vendor/bin/behat --tags="@team,@auth"

```

 

### Gestión de Base de Datos de Testing

 

#### Migrar Base de Datos de Test

```bash

make migrate-test

```

 

Ejecuta las migraciones en la base de datos de testing.

 

#### Resetear Base de Datos de Test

```bash

make reset-test-db

```

 

Elimina, recrea y migra la base de datos de testing.

 

### Entorno de Testing

 

Los tests se ejecutan con la configuración de `.env.test`:

- Base de datos: MariaDB en Docker (`mariadb:3306`)

- Entorno: `APP_ENV=test`

- Base de datos: `jee_api_test`

 

**Hooks de Base de Datos** (DatabaseContext):

- `@BeforeSuite`: Crea la base de datos y ejecuta migraciones

- `@BeforeScenario`: Limpia datos antes de cada escenario

- `@AfterScenario`: Limpieza adicional si es necesaria

 

---

 

## Configuración de Cobertura

 

### Configuración en PHPUnit

 

La cobertura de código está configurada en `phpunit.xml.dist`:

 

```xml

<coverage processUncoveredFiles="true">

    <include>

        <directory suffix=".php">src</directory>

    </include>

</coverage>

```

 

**Alcance de cobertura**:

- Directorio: `src/` (todo el código fuente)

- Archivos procesados: Todos los `.php`

 

### Generar Reportes de Cobertura

 

#### Reporte HTML

```bash

vendor/bin/phpunit --coverage-html coverage/

```

 

Genera un reporte HTML completo en el directorio `coverage/`. Abrir `coverage/index.html` en el navegador.

 

#### Reporte en Terminal

```bash

vendor/bin/phpunit --coverage-text

```

 

Muestra un resumen de cobertura en la terminal.

 

#### Reporte Clover (para CI)

```bash

vendor/bin/phpunit --coverage-clover coverage.xml

```

 

Genera un archivo XML compatible con herramientas de CI/CD.

 

### Objetivos de Cobertura

 

**Objetivos recomendados**:

- **Cobertura de líneas**: > 80%

- **Cobertura de funciones**: > 90%

- **Cobertura de clases**: > 85%

 

**Prioridades de cobertura**:

1. **Alta prioridad** (> 90%):

   - Value Objects (reglas de dominio críticas)

   - Servicios de aplicación (use cases)

   - Validadores y transformadores

 

2. **Media prioridad** (> 70%):

   - Repositorios

   - Controladores

   - Event listeners

 

3. **Baja prioridad** (> 50%):

   - DTOs y entidades simples

   - Configuración

 

**Exclusiones razonables**:

- Código generado automáticamente

- Migraciones de base de datos

- Scripts de deployment

- Archivos de configuración

 

---

 

## Buenas Prácticas

 

### Generales

 

1. **Ejecutar tests frecuentemente**: Antes de cada commit y push

2. **Tests independientes**: Cada test debe poder ejecutarse de forma aislada

3. **Tests determinísticos**: El mismo test debe producir el mismo resultado siempre

4. **Mantener tests rápidos**: Tests unitarios < 100ms, integración < 1s

5. **No hacer commits de tests fallidos**: Todos los tests deben pasar en `main`

6. **Un concepto por test**: Cada test verifica un comportamiento específico

7. **Nombres descriptivos**: El nombre del test debe explicar qué se está probando

 

### Tests Unitarios

 

1. **Usar mocks para dependencias externas**: Base de datos, APIs, filesystem

2. **Aplicar AAA (Arrange, Act, Assert)**: Estructura clara de tests

3. **Usar Object Mothers**: Crear datos de prueba consistentes y reutilizables

4. **Tests de casos límite**: Probar valores nulos, vacíos, extremos

5. **Tests de excepciones**: Verificar que se lanzan las excepciones correctas

6. **Evitar lógica en tests**: Los tests deben ser simples y directos

7. **Un assert por test** (cuando sea posible): Facilita identificar fallos

 

### Tests de Integración (Behat)

 

1. **Features en lenguaje de negocio**: Evitar detalles técnicos innecesarios

2. **Reutilizar steps comunes**: Usar `ApiContext` y contextos compartidos

3. **Datos de prueba realistas**: Usar datos que representen casos reales

4. **Limpiar datos después de tests**: Evitar efectos secundarios entre tests

5. **Usar tags apropiadamente**: Facilita filtrado y organización

6. **Escenarios independientes**: No depender del orden de ejecución

7. **Verificar respuestas completas**: Status code, estructura, datos

 

### Mantenimiento

 

1. **Refactorizar tests junto con código**: Los tests también necesitan mantenimiento

2. **Eliminar tests obsoletos**: Si cambia el comportamiento, actualizar o eliminar

3. **Actualizar Object Mothers**: Mantenerlos sincronizados con el dominio

4. **Revisar cobertura regularmente**: Identificar áreas sin tests

5. **Documentar casos complejos**: Explicar el "por qué" en comentarios

6. **Revisar tests en code reviews**: Igual de importante que el código de producción

 

---

 

## Ejemplos

 

### Ejemplo 1: Test Unitario Simple (Value Object)

 

**Archivo**: `tests/Unit/Web/Team/Domain/TeamNameTest.php`

 

```php

<?php

 

declare(strict_types=1);

 

namespace Tests\Unit\Web\Team\Domain;

 

use App\Web\Team\Domain\TeamName;

use InvalidArgumentException;

use PHPUnit\Framework\TestCase;

 

final class TeamNameTest extends TestCase

{

    public function testItShouldCreateValidTeamName(): void

    {

        // Arrange & Act

        $teamName = new TeamName('Best Team Ever');

 

        // Assert

        $this->assertSame('Best Team Ever', $teamName->value());

    }

 

    public function testItShouldThrowExceptionWhenNameIsEmpty(): void

    {

        // Assert

        $this->expectException(InvalidArgumentException::class);

        $this->expectExceptionMessage('Team name cannot be empty');

 

        // Act

        new TeamName('');

    }

 

    public function testItShouldThrowExceptionWhenNameIsTooLong(): void

    {

        // Arrange

        $longName = str_repeat('a', 101);

 

        // Assert

        $this->expectException(InvalidArgumentException::class);

 

        // Act

        new TeamName($longName);

    }

}

```

 

### Ejemplo 2: Test Unitario con Mocks (Use Case)

 

**Archivo**: `tests/Unit/Web/Team/Application/TeamCreatorTest.php`

 

```php

<?php

 

declare(strict_types=1);

 

namespace Tests\Unit\Web\Team\Application;

 

use App\Web\Team\Application\TeamCreator;

use App\Web\Team\Domain\Team;

use App\Web\Team\Domain\TeamRepository;

use PHPUnit\Framework\MockObject\MockObject;

use PHPUnit\Framework\TestCase;

use Tests\Unit\Web\Team\Domain\TeamMother;

 

final class TeamCreatorTest extends TestCase

{

    private TeamRepository|MockObject $teamRepository;

    private TeamCreator $teamCreator;

 

    protected function setUp(): void

    {

        $this->teamRepository = $this->createMock(TeamRepository::class);

        $this->teamCreator = new TeamCreator($this->teamRepository);

    }

 

    public function testItShouldCreateATeam(): void

    {

        // Arrange

        $team = TeamMother::create();

 

        $this->teamRepository

            ->expects($this->once())

            ->method('save')

            ->with($this->equalTo($team));

 

        // Act

        $this->teamCreator->create($team);

 

        // Assert (verificación implícita en expects)

    }

}

```

 

### Ejemplo 3: Object Mother

 

**Archivo**: `tests/Unit/Web/Team/Domain/TeamMother.php`

 

```php

<?php

 

declare(strict_types=1);

 

namespace Tests\Unit\Web\Team\Domain;

 

use App\Web\Team\Domain\Team;

use App\Web\Team\Domain\TeamId;

use App\Web\Team\Domain\TeamName;

 

final class TeamMother

{

    public static function create(

        ?TeamId $id = null,

        ?TeamName $name = null,

        ?string $description = null

    ): Team {

        return new Team(

            $id ?? TeamIdMother::create(),

            $name ?? TeamNameMother::create('Default Team'),

            $description ?? 'Default team description'

        );

    }

 

    public static function random(): Team

    {

        return self::create(

            TeamIdMother::random(),

            TeamNameMother::random()

        );

    }

 

    public static function withName(string $name): Team

    {

        return self::create(

            name: TeamNameMother::create($name)

        );

    }

 

    public static function withId(string $id): Team

    {

        return self::create(

            id: TeamIdMother::create($id)

        );

    }

}

```

 

### Ejemplo 4: Feature de Behat

 

**Archivo**: `tests/Behat/Web/Team/team_create.feature`

 

```gherkin

@team @auth

Feature: Create Team

  In order to organize players in the platform

  As an authenticated user

  I want to create a new team

 

  Background:

    Given the database is clean

 

  Scenario: Successfully create a team

    Given I am authenticated as "test@example.com" with password "password123"

    When I send a PUT request to "/api/team/550e8400-e29b-41d4-a716-446655440000" with body:

      """

      {

        "name": "Best Team",

        "description": "The best gaming team in the world",

        "tag": "BEST"

      }

      """

    Then the response status code should be 200

    And the response should have "id" property

    And the JSON node "name" should be equal to "Best Team"

    And the JSON node "tag" should be equal to "BEST"

 

  Scenario: Fail to create team without authentication

    Given I am not authenticated

    When I send a PUT request to "/api/team/550e8400-e29b-41d4-a716-446655440000" with body:

      """

      {

        "name": "Best Team"

      }

      """

    Then the response status code should be 401

 

  Scenario: Fail to create team with empty name

    Given I am authenticated as "test@example.com" with password "password123"

    When I send a PUT request to "/api/team/550e8400-e29b-41d4-a716-446655440000" with body:

      """

      {

        "name": "",

        "description": "Team without name"

      }

      """

    Then the response status code should be 400

 

  Scenario: Fail to create team with duplicate name

    Given I am authenticated as "test@example.com" with password "password123"

    And there is a team with name "Existing Team"

    When I send a PUT request to "/api/team/550e8400-e29b-41d4-a716-446655440000" with body:

      """

      {

        "name": "Existing Team"

      }

      """

    Then the response status code should be 409

```

 

### Ejemplo 5: Context Personalizado

 

**Archivo**: `tests/Behat/Web/Team/TeamTestContext.php`

 

```php

<?php declare(strict_types=1);

namespace Tests\Behat\Web\Team;


use Behat\Behat\Context\Context;

use Doctrine\ORM\EntityManagerInterface;

use Tests\Behat\Shared\Infrastructure\Behat\ApiContext;

use App\Web\Team\Domain\Team;

final class TeamTestContext implements Context
{

    public function __construct(

        private readonly ApiContext $apiContext,

        private readonly EntityManagerInterface $entityManager

    ) {}

    /**
     * @BeforeScenario @team

     */

    public function setUp(): void

    {

        // Preparación antes de cada escenario de team

    }

 

    /**

     * @AfterScenario @team

     */

    public function tearDown(): void

    {

        // Limpieza después de cada escenario

    }

 

    /**

     * @Given there is a team with name :name

     */

    public function thereIsATeamWithName(string $name): void

    {

        $team = new Team(

            id: '550e8400-e29b-41d4-a716-446655440000',

            name: $name,

            description: 'Test team'

        );

 

        $this->entityManager->persist($team);

        $this->entityManager->flush();

    }

 

    /**

     * @Given there are :count teams

     */

    public function thereAreTeams(int $count): void

    {

        for ($i = 0; $i < $count; $i++) {

            $team = new Team(

                id: sprintf('550e8400-e29b-41d4-a716-%012d', $i),

                name: "Team $i",

                description: "Description $i"

            );

 

            $this->entityManager->persist($team);

        }

 

        $this->entityManager->flush();

    }

}

```

 

---

 

## Recursos Adicionales

 

### Archivos de Configuración

 

- **PHPUnit**: `phpunit.xml.dist`

- **Behat**: `behat.yml`

- **Entorno de test**: `.env.test`

- **Makefile**: Comandos predefinidos para testing

 

### Documentación Externa

 

- [PHPUnit Documentation](https://phpunit.de/documentation.html)

- [Behat Documentation](https://docs.behat.org/)

- [Gherkin Syntax](https://cucumber.io/docs/gherkin/)

- [Symfony Testing](https://symfony.com/doc/current/testing.html)

 

### Comandos Útiles

 

```bash

# Ver todos los comandos disponibles

make help

 

# Limpiar caché de test

make cache-clear-test

 

# Ver logs de tests

docker compose logs -f php

 

# Acceder al contenedor para debugging

docker compose exec php bash

```

 

---

 

**Nota**: Este documento debe mantenerse actualizado conforme evolucionan las convenciones y prácticas de testing del proyecto.
