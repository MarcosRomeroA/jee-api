## Table of Contents
1. [Project Overview](#project-overview)
2. [Architecture](#architecture)
3. [Directory Structure](#directory-structure)
4. [Development Workflows](#development-workflows)
5. [Code Patterns & Conventions](#code-patterns--conventions)
6. [Testing Strategy](#testing-strategy)
7. [Adding New Features](#adding-new-features)
8. [Common Tasks](#common-tasks)
9. [Configuration & Environment](#configuration--environment)
10. [Important Rules](#important-rules)

---

## Project Overview

**Juga en Equipo (JEE)** is a social network for esports players, enabling team formation, tournament creation, and professional networking.

### Technology Stack
- **PHP 8.3** with strict types
- **Symfony 7.0** (framework)
- **Doctrine ORM 3.x** (persistence)
- **MariaDB 11** (database)
- **RabbitMQ** (message broker for async operations)
- **Mercure** (real-time notifications)
- **JWT** (authentication via lexik/jwt-authentication-bundle)
- **Behat** (acceptance/integration tests)
- **PHPUnit 9.5** (unit tests)
- **Docker** (containerization)

### Key Features
- User authentication and profiles
- Player management with rank verification (Riot Games, Steam APIs)
- Team creation and management
- Tournament organization
- Social feed with posts, likes, and comments
- Real-time notifications via Mercure
- Direct messaging between users

---

## Architecture

This project follows **Domain-Driven Design (DDD)** with **Hexagonal Architecture** (Ports & Adapters), **CQRS** (Command Query Responsibility Segregation), and **Event Sourcing** patterns.

### Architectural Layers

```
┌─────────────────────────────────────────────────────────┐
│              Apps Layer (HTTP Adapters)                 │
│  Controllers that handle HTTP requests/responses        │
│  Location: src/Apps/Web/{Context}/{UseCase}/           │
└─────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────┐
│           Application Layer (Use Cases)                 │
│  Commands, Queries, Handlers, Application Services      │
│  Location: src/Contexts/{Context}/Application/         │
└─────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────┐
│              Domain Layer (Business Logic)              │
│  Entities, Value Objects, Events, Repositories          │
│  Location: src/Contexts/{Context}/Domain/              │
└─────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────┐
│       Infrastructure Layer (Technical Details)          │
│  Doctrine Repositories, External API Clients            │
│  Location: src/Contexts/{Context}/Infrastructure/      │
└─────────────────────────────────────────────────────────┘
```

### Bounded Contexts

The application is divided into **11 bounded contexts**:

1. **Auth** - Login, token refresh
2. **User** - User profiles, followers, password management
3. **Player** - In-game player data, rank verification
4. **Game** - Game definitions, ranks, roles
5. **Team** - Team management
6. **Tournament** - Tournament organization, matches, results
7. **Post** - Social feed functionality
8. **Notification** - Notification generation and delivery
9. **Conversation** - Direct messaging
10. **Participant** - Tournament participant tracking
11. **Shared** - Cross-cutting concerns and shared kernel

**Location:** `src/Contexts/Web/{Context}/`

---

## Directory Structure

```
jee-api/
├── bin/                           # Symfony console scripts
├── config/                        # Configuration files
│   ├── packages/                  # Bundle configurations
│   ├── routes/                    # Route definitions
│   │   └── web/                   # Per-context route files
│   └── services.yaml              # Service container config
├── docker/                        # Docker configurations
├── migrations/                    # Doctrine migrations
├── public/                        # Web root (index.php)
├── src/
│   ├── Apps/                      # HTTP Layer (Controllers)
│   │   └── Web/
│   │       ├── Auth/
│   │       ├── User/
│   │       ├── Player/
│   │       └── ...                # One folder per context
│   ├── Contexts/                  # Business Logic Layer
│   │   ├── Shared/                # Shared kernel
│   │   │   ├── Domain/            # Shared abstractions
│   │   │   │   ├── CQRS/          # CQRS interfaces
│   │   │   │   ├── Aggregate/     # AggregateRoot base class
│   │   │   │   ├── ValueObject/   # Base value objects
│   │   │   │   └── ...
│   │   │   └── Infrastructure/    # Shared implementations
│   │   │       ├── CQRS/          # CQRS bus implementations
│   │   │       ├── Symfony/       # Framework integration
│   │   │       ├── Persistence/   # Doctrine base classes
│   │   │       └── ...
│   │   └── Web/                   # Bounded contexts
│   │       ├── User/
│   │       │   ├── Domain/        # Entities, VOs, Events, Repos
│   │       │   ├── Application/   # Commands, Queries, Handlers
│   │       │   └── Infrastructure/# Doctrine repositories
│   │       ├── Player/
│   │       └── ...
│   └── Kernel.php
├── templates/                     # Email templates
├── tests/
│   ├── Behat/                     # Acceptance tests
│   │   ├── Shared/
│   │   └── Web/                   # Per-context test contexts
│   └── Unit/                      # PHPUnit unit tests
├── translations/                  # i18n files
├── var/                           # Cache, logs (gitignored)
├── .env.template                  # Environment template
├── composer.json                  # PHP dependencies
├── Makefile                       # Development commands
├── docker-compose.yaml            # Production containers
└── docker-compose.dev.yaml        # Development containers
```

---

## Development Workflows

### Environment Setup

**Docker-based development** (recommended):

```bash
# Start development environment
make dev

# Build from scratch
make build-dev

# Enter the Symfony container
make exec

# View logs
make logs

# Stop containers
make stop

# Completely remove containers
make down
```

**Manual setup** (without Docker):
```bash
# Install dependencies
composer install

# Copy environment file
cp .env.template .env
# Edit .env with your database credentials

# Generate JWT keys
php bin/console lexik:jwt:generate-keypair

# Create database
php bin/console doctrine:migrations:migrate

# Load fixtures (optional)
php bin/console doctrine:fixtures:load --append

# Start server
symfony serve
# or
php -S localhost:8000 -t public/
```

### Database Management

```bash
# Create a new migration
make migration-diff

# Run migrations (dev)
make migrate

# Run migrations (test)
make migrate-test

# Reset test database completely
make reset-test-db
```

### Testing

```bash
# Run all tests (Behat + PHPUnit)
make test

# Run only Behat tests
make behat

# Run Behat tests with specific tag
make behat tag=mercure

# Run only PHPUnit tests
make unit

# Run tests with fresh database
make test-clean

# Verbose test output
make test-verbose
```

### Common Commands

```bash
# Clear cache
make clean-cache

# View all routes
make routes

# View routes for a specific context
make routes-player

# View help
make help
```

### Async Workers

For background jobs (emails, notifications):
```bash
php bin/console messenger:consume -vvv
```

---

## Code Patterns & Conventions

### 1. Strict Types Declaration

**ALWAYS** start PHP files with:
```php
<?php declare(strict_types=1);
```

### 2. Namespace Convention

- **Controllers:** `App\Apps\Web\{Context}\{UseCase}\`
- **Domain:** `App\Contexts\Web\{Context}\Domain\`
- **Application:** `App\Contexts\Web\{Context}\Application\{UseCase}\`
- **Infrastructure:** `App\Contexts\Web\{Context}\Infrastructure\`

### 3. Controller Pattern

Controllers extend `ApiController` and dispatch Commands or Queries:

```php
<?php declare(strict_types=1);

namespace App\Apps\Web\User\Create;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateUserController extends ApiController
{
    public function __invoke(Request $request, string $id): Response
    {
        // 1. Parse and validate request
        $input = CreateUserRequest::fromHttp($request, $id);
        $this->validateRequest($input);

        // 2. Convert to Command
        $command = $input->toCommand();

        // 3. Dispatch Command
        $this->dispatch($command);

        // 4. Return response
        return $this->successEmptyResponse();
    }
}
```

**Query example:**
```php
public function __invoke(string $id): Response
{
    $query = new FindUserQuery($id);
    $response = $this->ask($query);
    return $this->successResponse($response);
}
```

### 4. CQRS Pattern

**Commands** (write operations):
- `{UseCase}Command.php` - Immutable DTO
- `{UseCase}CommandHandler.php` - Implements `CommandHandler` interface
- Returns `void`

**Queries** (read operations):
- `{UseCase}Query.php` - Immutable DTO
- `{UseCase}QueryHandler.php` - Implements `QueryHandler` interface
- Returns `Response` object

**Example Command Handler:**
```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;

final readonly class CreateUserCommandHandler implements CommandHandler
{
    public function __construct(
        private UserCreator $creator,
    ) {}

    public function __invoke(CreateUserCommand $command): void
    {
        // 1. Create value objects
        $id = new Uuid($command->id);
        $firstname = new FirstnameValue($command->firstname);
        $lastname = new LastnameValue($command->lastname);
        // ...

        // 2. Delegate to application service
        $this->creator->__invoke($id, $firstname, $lastname, ...);
    }
}
```

### 5. Domain Entity Pattern

Entities extend `AggregateRoot` and use **static factory methods**:

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[Embedded(class: FirstnameValue::class, columnPrefix: false)]
    private FirstnameValue $firstname;

    // Private constructor
    private function __construct(
        Uuid $id,
        FirstnameValue $firstname,
        // ...
    ) {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->createdAt = new \DateTimeImmutable();
    }

    // Static factory method
    public static function create(
        Uuid $id,
        FirstnameValue $firstname,
        // ...
    ): self {
        $user = new self($id, $firstname, ...);

        // Record domain event
        $user->record(new UserCreatedDomainEvent($id));

        return $user;
    }

    // Business methods
    public function update(...): self {
        // Update logic
        $this->record(new UserUpdatedDomainEvent($this->id));
        return $this;
    }
}
```

### 6. Value Objects

Value Objects are **immutable** and use Doctrine **embeddables**:

```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\ValueObject;

use App\Contexts\Shared\Domain\ValueObject\StringValueObject;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class UsernameValue extends StringValueObject
{
    #[ORM\Column(name: 'username', type: 'string', length: 50, unique: true)]
    protected string $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        parent::__construct($value);
    }

    private function validate(string $value): void
    {
        if (strlen($value) < 3 || strlen($value) > 50) {
            throw new \InvalidArgumentException('Username must be 3-50 chars');
        }
    }
}
```

### 7. Repository Pattern

**Interface in Domain:**
```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

interface UserRepository
{
    public function save(User $user): void;
    public function findById(Uuid $id): ?User;
    public function findByUsername(UsernameValue $username): ?User;
}
```

**Implementation in Infrastructure:**
```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Infrastructure\Persistence;

use App\Contexts\Web\User\Domain\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

final class MysqlUserRepository implements UserRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function findById(Uuid $id): ?User
    {
        return $this->entityManager->find(User::class, $id);
    }
}
```

### 8. Domain Events

**Publish events from entities:**
```php
public static function create(...): self {
    $entity = new self(...);
    $entity->record(new EntityCreatedDomainEvent($id));
    return $entity;
}
```

**Subscribe to events in other contexts:**
```php
<?php declare(strict_types=1);

namespace App\Contexts\Web\Notification\Application\Subscribers;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Web\Post\Domain\Events\PostLikedDomainEvent;

final readonly class CreateNotificationOnPostLikedSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private NotificationCreator $creator,
    ) {}

    public static function subscribedTo(): array
    {
        return [PostLikedDomainEvent::class];
    }

    public function __invoke(PostLikedDomainEvent $event): void
    {
        // Create notification when post is liked
        $this->creator->createPostLikedNotification(...);
    }
}
```

### 9. Routing Convention

Routes are defined in YAML per context:
`config/routes/web/{context}.yaml`

```yaml
create_user:
  path: /user/{id}
  controller: App\Apps\Web\User\Create\CreateUserController
  methods: [ PUT ]

find_user:
  path: /user/{id}
  controller: App\Apps\Web\User\Find\FindUserController
  methods: [ GET ]
  defaults: { auth: true }  # Requires JWT authentication
```
**REST Conventions:**
- `PUT /resource/{id}` - Create or update
- `GET /resource/{id}` - Find by ID
- `GET /resources` - Search/list
- `DELETE /resource/{id}` - Delete
- `POST /resource/{id}/{action}` - Custom actions

### 10. Service Configuration

Services are **auto-wired** and **auto-configured** in `config/services.yaml`:

```yaml
_defaults:
    autowire: true
    autoconfigure: true
# Auto-tag handlers
_instanceof:
    App\Contexts\Shared\Domain\CQRS\Command\CommandHandler:
        tags: ["app.command_handler"]
    App\Contexts\Shared\Domain\CQRS\Query\QueryHandler:
        tags: ["app.query_handler"]
    App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber:
        tags: ["app.domain_event_subscriber"]
```

### 11. API Response Format

```json
{
  "data": {
    "id": "uuid-here",
    "username": "player123",
    ...
  }
}
```

For collections:
```json
{
  "data": [...],
  "total": 100,
  "page": 1,
  "limit": 20
}
```

---

## Testing Strategy

### Behat (Acceptance/Integration Tests)

**Location:** `tests/Behat/Web/{Context}/`

Behat tests verify end-to-end functionality through HTTP requests.

**Test Context Structure:**
```php
<?php declare(strict_types=1);
namespace App\Tests\Behat\Web\User;
use App\Tests\Behat\Shared\Infrastructure\Behat\ApiContext;
use Behat\Behat\Context\Context;
final class UserTestContext implements Context
{
    public function __construct(
        private ApiContext $apiContext,
    ) {}
    /**
     * @When I create a user with id :id
     */
    public function iCreateUserWithId(string $id): void
    {
        $this->apiContext->sendRequest('PUT', "/user/$id", [
            'firstname' => 'John',
            'lastname' => 'Doe',
            // ...
        ]);
    }
    /**
     * @Then the user should be created
     */
    public function theUserShouldBeCreated(): void
    {
        $this->apiContext->assertResponseStatusCode(200);
    }
}
```

**Run Behat tests:**
```bash
make behat
make behat tag=user
```

### PHPUnit (Unit Tests)

**Location:** `tests/Unit/`

Unit tests verify individual components in isolation.

**Use "Mother" pattern** for test data:
```php
<?php declare(strict_types=1);
namespace App\Tests\Unit\Web\User\Domain;
final class UserMother
{
    public static function create(
        ?Uuid $id = null,
        ?string $username = null,
    ): User {
        return User::create(
            $id ?? UuidMother::random(),
            new FirstnameValue('Test'),
            new LastnameValue('User'),
            new UsernameValue($username ?? 'testuser'),
            new EmailValue('test@example.com'),
            new PasswordValue('password123'),
        );
    }
}
```

**Run PHPUnit tests:**
```bash
make unit
```

### Test Database

Tests use a separate database with `_test` suffix.

**Reset test database:**
```bash
make reset-test-db
```

---

## Adding New Features

### Step-by-Step Guide

#### 1. **Create a New Use Case (e.g., "Update User Email")**

##### Domain Layer
Create domain exception if needed:
```php
// src/Contexts/Web/User/Domain/Exception/InvalidEmailException.php
<?php declare(strict_types=1);
namespace App\Contexts\Web\User\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class InvalidEmailException extends ApiException
{
    public function __construct()
    {
        parent::__construct(
            'Invalid email format',
            'invalid_email_exception',
            Response::HTTP_BAD_REQUEST
        );
    }
}
```

Add method to domain entity:
```php
// src/Contexts/Web/User/Domain/User.php
public function updateEmail(EmailValue $email): self
{
    $this->email = $email;
    $this->record(new UserEmailUpdatedDomainEvent($this->id, $email));
    return $this;
}
```

Create domain event:
```php
// src/Contexts/Web/User/Domain/Events/UserEmailUpdatedDomainEvent.php
<?php declare(strict_types=1);
namespace App\Contexts\Web\User\Domain\Events;
use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
final readonly class UserEmailUpdatedDomainEvent extends DomainEvent
{
    public function __construct(
        public Uuid $userId,
        public EmailValue $email,
    ) {
        parent::__construct();
    }
}
```

##### Application Layer
Create application service:
```php
// src/Contexts/Web/User/Application/UpdateEmail/UserEmailUpdater.php
<?php declare(strict_types=1);
namespace App\Contexts\Web\User\Application\UpdateEmail;
final readonly class UserEmailUpdater
{
    public function __construct(
        private UserRepository $repository,
    ) {}
    public function __invoke(Uuid $userId, EmailValue $email): void
    {
        $user = $this->repository->findById($userId);
        if (!$user) {
            throw new UserNotFoundException();
        }
        $user->updateEmail($email);
        $this->repository->save($user);
    }
}
```

Create Command:
```php
// src/Contexts/Web/User/Application/UpdateEmail/UpdateUserEmailCommand.php
<?php declare(strict_types=1);
namespace App\Contexts\Web\User\Application\UpdateEmail;
use App\Contexts\Shared\Domain\CQRS\Command\Command;
final readonly class UpdateUserEmailCommand implements Command
{
    public function __construct(
        public string $userId,
        public string $email,
    ) {}
}
```

Create Command Handler:
```php
// src/Contexts/Web/User/Application/UpdateEmail/UpdateUserEmailCommandHandler.php
<?php declare(strict_types=1);
namespace App\Contexts\Web\User\Application\UpdateEmail;
use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
final readonly class UpdateUserEmailCommandHandler implements CommandHandler
{
    public function __construct(
        private UserEmailUpdater $updater,
    ) {}
    public function __invoke(UpdateUserEmailCommand $command): void
    {
        $this->updater->__invoke(
            new Uuid($command->userId),
            new EmailValue($command->email),
        );
    }
}
```

##### Apps Layer (Controller)
Create request DTO:
```php
// src/Apps/Web/User/UpdateEmail/UpdateUserEmailRequest.php
<?php declare(strict_types=1);
namespace App\Apps\Web\User\UpdateEmail;
use Symfony\Component\Validator\Constraints as Assert;
final readonly class UpdateUserEmailRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
    ) {}
    public static function fromHttp(Request $request): self
    {
        $data = json_decode($request->getContent(), true);
        return new self($data['email'] ?? '');
    }
    public function toCommand(string $userId): UpdateUserEmailCommand
    {
        return new UpdateUserEmailCommand($userId, $this->email);
    }
}
```

Create controller:
```php
// src/Apps/Web/User/UpdateEmail/UpdateUserEmailController.php
<?php declare(strict_types=1);
namespace App\Apps\Web\User\UpdateEmail;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
final class UpdateUserEmailController extends ApiController
{
    public function __invoke(Request $request, string $id): Response
    {
        $input = UpdateUserEmailRequest::fromHttp($request);
        $this->validateRequest($input);
        $command = $input->toCommand($id);
        $this->dispatch($command);
        return $this->successEmptyResponse();
    }
}
```

##### Add Route
```yaml
# config/routes/web/user.yaml
update_user_email:
  path: /user/{id}/email
  controller: App\Apps\Web\User\UpdateEmail\UpdateUserEmailController
  methods: [ PUT ]
  defaults: { auth: true }
```

#### 2. **Add Tests**

**Behat feature:**
```gherkin
# tests/Behat/Web/User/update_email.feature
Feature: Update user email
  Scenario: User updates their email
    Given I am authenticated as user "uuid-123"
    When I update my email to "newemail@example.com"
    Then the response status code should be 200
    And the user email should be "newemail@example.com"
```

**PHPUnit test:**
```php
// tests/Unit/Web/User/Application/UpdateEmail/UserEmailUpdaterTest.php
final class UserEmailUpdaterTest extends TestCase
{
    public function testItUpdatesUserEmail(): void
    {
        $repository = $this->createMock(UserRepository::class);
        $user = UserMother::create();
        $repository->expects($this->once())
            ->method('findById')
            ->willReturn($user);
        $updater = new UserEmailUpdater($repository);
        $updater->__invoke(
            $user->getId(),
            new EmailValue('new@example.com')
        );
        $this->assertEquals('new@example.com', $user->getEmail()->value());
    }
}
```

#### 3. **Create Migration**

```bash
make migration-diff
```

Review and edit the generated migration, then run:
```bash
make migrate
```

#### 4. **Run Tests**

```bash
make test-clean
```

---

## Common Tasks

### Adding a New Bounded Context

1. Create directory structure:
```bash
mkdir -p src/Contexts/Web/NewContext/{Domain,Application,Infrastructure}
mkdir -p src/Apps/Web/NewContext
```

2. Create route file:
```yaml
# config/routes/web/newcontext.yaml
```

3. Add Behat test context if needed:
```php
# tests/Behat/Web/NewContext/NewContextTestContext.php
```

### Adding External API Integration

1. Define interface in Domain:
```php
// src/Contexts/Web/Player/Domain/Service/RankVerifier.php
interface RankVerifier
{
    public function verify(string $playerId): RankInfo;
}
```

2. Implement in Infrastructure:
```php
// src/Contexts/Web/Player/Infrastructure/RankVerification/Riot/RiotRankVerifier.php
final class RiotRankVerifier implements RankVerifier
{
    public function __construct(
        private RiotApiClient $client,
    ) {}
    public function verify(string $playerId): RankInfo
    {
        // API call logic
    }
}
```

3. Use Decorator pattern for caching:
```php
// src/Contexts/Web/Player/Infrastructure/RankVerification/Decorator/CachedRankVerifier.php
final class CachedRankVerifier implements RankVerifier
{
    public function __construct(
        private RankVerifier $inner,
        private CacheInterface $cache,
    ) {}
    public function verify(string $playerId): RankInfo
    {
        return $this->cache->get(
            "rank_$playerId",
            fn() => $this->inner->verify($playerId)
        );
    }
}
```

4. Configure in `config/services.yaml`:
```yaml
App\Contexts\Web\Player\Infrastructure\RankVerification\Decorator\CachedRankVerifier:
    decorates: App\Contexts\Web\Player\Infrastructure\RankVerification\Riot\RiotRankVerifier
    arguments:
        $inner: "@.inner"
```

### Image Processing with Thumbnails

The project includes an image processing system for profile photos that automatically generates optimized thumbnails in WebP format.

**Components:**
- `ProfileImageOptimizer` - Validates and optimizes images, generates multiple sizes
- `ProfileImageUploader` - Uploads all versions to R2 storage
- `ProfileImageResult` - DTO containing all image versions and metadata

**Generated Sizes:**
| Size | Dimensions | Suffix | Use Case |
|------|------------|--------|----------|
| Main | 512x512 | (none) | Profile page, full view |
| Medium | 128x128 | `_128` | Comments, lists |
| Small | 64x64 | `_64` | Avatars, thumbnails |

**Image Specifications:**
- **Format:** WebP (converted from jpg, png, webp)
- **Quality:** 85%
- **Max upload size:** 5MB
- **Crop method:** Cover (center crop to square)

**Storage Path:** `user/profile/{uuid}.webp`, `user/profile/{uuid}_128.webp`, `user/profile/{uuid}_64.webp`

**Usage Example:**
```php
// In UserProfilePhotoUpdater
$result = $this->optimizer->optimize($tempFilePath);
$filename = $this->uploader->upload($result, $userId);
```

**Dependencies:**
- `intervention/image` - Image manipulation library
- `league/flysystem` - Filesystem abstraction (R2/S3 storage)

### Adding Real-time Notifications (Mercure)

Domain event subscribers can publish to Mercure:

```php
final readonly class PublishNotificationToMercureSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private HubInterface $hub,
    ) {}
    public static function subscribedTo(): array
    {
        return [NotificationCreatedDomainEvent::class];
    }
    public function __invoke(NotificationCreatedDomainEvent $event): void
    {
        $update = new Update(
            "notifications/{$event->userId}",
            json_encode(['notification' => $event->notificationData])
        );
        $this->hub->publish($update);
    }
}
```

---

## Configuration & Environment

### Environment Variables

Copy `.env.template` to `.env` and configure:

```bash
# Symfony
APP_ENV=dev
APP_SECRET=your-secret-key
# Database
DATABASE_URL="mysql://user:pass@127.0.0.1:3306/jee_db"
# JWT
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your-passphrase
# Messenger (RabbitMQ)
MESSENGER_TRANSPORT_DSN=amqp://guest:guest@rabbitmq:5672/%2f
# Mailer
MAILER_DSN=smtp://user:pass@mailserver:port
MAILER_FROM_EMAIL=noreply@example.com
MAILER_FROM_NAME="Juga en Equipo"
# App URL (for email links)
APP_URL=http://localhost:8000
# External APIs
RIOT_API_KEY=your-riot-api-key
RIOT_DEFAULT_REGION=na1
STEAM_API_KEY=your-steam-api-key
# Mercure
MERCURE_URL=http://mercure/.well-known/mercure
MERCURE_PUBLIC_URL=http://localhost:3000/.well-known/mercure
MERCURE_JWT_SECRET=your-mercure-jwt-secret
```

### Doctrine Configuration

**Custom UUID type** is registered in `config/packages/doctrine.yaml`:
```yaml
doctrine:
  dbal:
    types:
      uuid: App\Contexts\Shared\Infrastructure\Persistence\Doctrine\UuidType
```

**Mappings** read from `src/Contexts` directory using PHP 8 attributes.

### Messenger (Async Jobs)

Configure transports in `config/packages/messenger.yaml`:
```yaml
framework:
    messenger:
        transports:
            async: '%env(MESSENGER_TRANSPORT_DSN)%'
        routing:
            'App\Contexts\Shared\Domain\Email\SendEmailMessage': async
```

---

## Important Rules

### When Working with This Codebase

1. **Always use strict types:** `declare(strict_types=1);`

2. **Never violate bounded context boundaries:**
   - Don't import domain entities from one context into another
   - Use domain events for cross-context communication
   - Shared code belongs in `Contexts/Shared/`

3. **Follow CQRS:**
   - Commands modify state, return void
   - Queries read state, return Response objects
   - Never mix them

4. **Use value objects for all domain primitives:**
   - Never pass raw strings/ints to domain entities
   - Validate in value object constructors

5. **Entities are never created with `new` outside of factories:**
   - Use static factory methods (`Entity::create(...)`)
   - Record domain events in factories

6. **Repository interfaces belong in Domain layer:**
   - Implementations in Infrastructure layer
   - Use Doctrine only in Infrastructure

7. **Controllers are thin:**
   - Parse request → Validate → Dispatch → Return response
   - No business logic in controllers

8. **Test at multiple levels:**
   - Unit tests for domain logic
   - Behat tests for end-to-end flows
   - Use test database for integration tests

9. **Migrations are versioned:**
   - Never edit existing migrations
   - Create new migration for changes
   - Review auto-generated migrations before committing

10. **Use dependency injection:**
    - Constructor injection only
    - No service locator pattern
    - Leverage Symfony's autowiring

11. **API responses are consistent:**
    - Always wrap in `{"data": ...}`
    - Use `successResponse()`, `successEmptyResponse()`, etc.
    - Return proper HTTP status codes

12. **Authentication via JWT:**
    - Routes requiring auth: `defaults: { auth: true }`
    - Middleware validates JWT tokens
    - User ID extracted from token payload

### File Naming Conventions

- **Controllers:** `{UseCase}Controller.php` (e.g., `CreateUserController.php`)
- **Commands:** `{UseCase}Command.php` (e.g., `CreateUserCommand.php`)
- **Command Handlers:** `{UseCase}CommandHandler.php`
- **Queries:** `{UseCase}Query.php`
- **Query Handlers:** `{UseCase}QueryHandler.php`
- **Domain Events:** `{Entity}{Action}DomainEvent.php` (e.g., `UserCreatedDomainEvent.php`)
- **Value Objects:** `{Attribute}Value.php` (e.g., `UsernameValue.php`)
- **Repositories (interface):** `{Entity}Repository.php`
- **Repositories (impl):** `Mysql{Entity}Repository.php`

### Code Quality

- **No commented code** - Remove unused code
- **No debug statements** - Remove `var_dump`, `dd()`, etc.
- **Descriptive names** - Clear intent over brevity
- **Single Responsibility** - Classes do one thing well
- **Dependency Inversion** - Depend on abstractions, not concretions

---

## Summary for AI Assistants

When working with this codebase:

1. **Identify the bounded context** first
2. **Follow DDD layering:** Domain → Application → Infrastructure → Apps
3. **Use CQRS:** Commands for writes, Queries for reads
4. **Leverage domain events** for cross-context communication
5. **Test thoroughly** with Behat and PHPUnit
6. **Always use value objects** for domain primitives
7. **Keep controllers thin** - dispatch to CQRS handlers
8. **Use static factory methods** for entity creation
9. **Run migrations** after schema changes
10. **Follow strict types and naming conventions**

This architecture ensures **high cohesion, low coupling, and clear separation of concerns**. Each bounded context can evolve independently while domain events enable reactive, event-driven communication.

---

**For questions or issues, refer to the official Symfony documentation:**
- https://symfony.com/doc/current/index.html
- https://www.doctrine-project.org/projects/doctrine-orm/en/latest/
