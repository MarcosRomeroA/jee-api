<?php

declare(strict_types=1);

namespace App\Apps\Console;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Game\Domain\GameRank;
use App\Contexts\Web\Game\Domain\GameRole;
use App\Contexts\Web\Game\Domain\Rank;
use App\Contexts\Web\Game\Domain\Role;
use App\Contexts\Web\Tournament\Domain\TournamentStatus;
use App\Contexts\Web\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:load-fixtures',
    description: 'Carga datos de prueba en la base de datos'
)]
final class LoadFixturesCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Cargando fixtures de datos de prueba');

        try {
            // Limpiar datos existentes
            $io->section('Limpiando datos existentes...');
            $this->cleanDatabase();

            // Crear Tournament Statuses
            $io->section('Creando estados de torneos...');
            $this->createTournamentStatuses();

            // Crear Games
            $io->section('Creando juegos...');
            $games = $this->createGames();

            // Crear Roles
            $io->section('Creando roles...');
            $roles = $this->createRoles();

            // Crear GameRoles
            $io->section('Creando roles de juegos...');
            $this->createGameRoles($games, $roles);

            // Crear GameRanks
            $io->section('Creando rangos de juegos...');
            $this->createGameRanks($games);

            // Crear usuarios de prueba
            $io->section('Creando usuarios de prueba...');
            $this->createTestUsers();

            $this->entityManager->flush();

            $io->success('Fixtures cargados exitosamente!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Error al cargar fixtures: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function cleanDatabase(): void
    {
        // No limpiar users porque pueden tener datos reales
        // Solo limpiar las nuevas entidades
        $tables = [
            'team_player',
            'team_request',
            'tournament_team',
            'tournament_request',
            'player',
            'team',
            'tournament',
            'game_role',
            'game_rank',
            'role',
            'game',
            'tournament_status',
        ];

        foreach ($tables as $table) {
            $this->entityManager->getConnection()->executeStatement("DELETE FROM {$table}");
        }
    }

    private function createTournamentStatuses(): void
    {
        $statuses = [
            ['id' => 'created', 'name' => 'Created'],
            ['id' => 'active', 'name' => 'Active'],
            ['id' => 'deleted', 'name' => 'Deleted'],
            ['id' => 'archived', 'name' => 'Archived'],
            ['id' => 'finalized', 'name' => 'Finalized'],
            ['id' => 'suspended', 'name' => 'Suspended'],
        ];

        foreach ($statuses as $data) {
            $status = new TournamentStatus(new Uuid($data['id']), $data['name']);
            $this->entityManager->persist($status);
        }
    }

    private function createGames(): array
    {
        $gamesData = [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440080',
                'name' => 'Valorant',
                'description' => 'Tactical first-person shooter developed by Riot Games',
                'min' => 5,
                'max' => 5
            ],
            [
                'id' => '550e8400-e29b-41d4-a716-446655440081',
                'name' => 'League of Legends',
                'description' => 'Multiplayer online battle arena game developed by Riot Games',
                'min' => 5,
                'max' => 5
            ],
            [
                'id' => '550e8400-e29b-41d4-a716-446655440082',
                'name' => 'Counter-Strike 2',
                'description' => 'Tactical first-person shooter developed by Valve',
                'min' => 5,
                'max' => 5
            ],
            [
                'id' => '550e8400-e29b-41d4-a716-446655440083',
                'name' => 'Dota 2',
                'description' => 'Multiplayer online battle arena game developed by Valve',
                'min' => 5,
                'max' => 5
            ],
        ];

        $games = [];
        foreach ($gamesData as $data) {
            $game = new Game(
                new Uuid($data['id']),
                $data['name'],
                $data['description'],
                $data['min'],
                $data['max']
            );
            $this->entityManager->persist($game);
            $games[$data['name']] = $game;
        }

        return $games;
    }

    private function createRoles(): array
    {
        $rolesData = [
            ['id' => '650e8400-e29b-41d4-a716-446655440001', 'name' => 'Duelist', 'description' => 'Aggressive entry fragger'],
            ['id' => '650e8400-e29b-41d4-a716-446655440002', 'name' => 'Controller', 'description' => 'Controls map space and blocks vision'],
            ['id' => '650e8400-e29b-41d4-a716-446655440003', 'name' => 'Sentinel', 'description' => 'Defensive anchor and support'],
            ['id' => '650e8400-e29b-41d4-a716-446655440004', 'name' => 'Initiator', 'description' => 'Gathers information and sets up plays'],
            ['id' => '650e8400-e29b-41d4-a716-446655440005', 'name' => 'Top', 'description' => 'Top lane position'],
            ['id' => '650e8400-e29b-41d4-a716-446655440006', 'name' => 'Jungle', 'description' => 'Jungle position'],
            ['id' => '650e8400-e29b-41d4-a716-446655440007', 'name' => 'Mid', 'description' => 'Mid lane position'],
            ['id' => '650e8400-e29b-41d4-a716-446655440008', 'name' => 'ADC', 'description' => 'Attack Damage Carry - Bottom lane'],
            ['id' => '650e8400-e29b-41d4-a716-446655440009', 'name' => 'Support', 'description' => 'Support position'],
        ];

        $roles = [];
        foreach ($rolesData as $data) {
            $role = new Role(
                new Uuid($data['id']),
                $data['name'],
                $data['description']
            );
            $this->entityManager->persist($role);
            $roles[$data['name']] = $role;
        }

        return $roles;
    }

    private function createGameRoles(array $games, array $roles): void
    {
        // Valorant roles
        $valorantGameRoles = [
            ['id' => '750e8400-e29b-41d4-a716-446655440001', 'role' => 'Duelist'],
            ['id' => '750e8400-e29b-41d4-a716-446655440002', 'role' => 'Controller'],
            ['id' => '750e8400-e29b-41d4-a716-446655440003', 'role' => 'Sentinel'],
            ['id' => '750e8400-e29b-41d4-a716-446655440004', 'role' => 'Initiator'],
        ];

        foreach ($valorantGameRoles as $data) {
            $gameRole = new GameRole(
                new Uuid($data['id']),
                $roles[$data['role']],
                $games['Valorant']
            );
            $this->entityManager->persist($gameRole);
        }

        // League of Legends roles
        $lolGameRoles = [
            ['id' => '750e8400-e29b-41d4-a716-446655440005', 'role' => 'Top'],
            ['id' => '750e8400-e29b-41d4-a716-446655440006', 'role' => 'Jungle'],
            ['id' => '750e8400-e29b-41d4-a716-446655440007', 'role' => 'Mid'],
            ['id' => '750e8400-e29b-41d4-a716-446655440008', 'role' => 'ADC'],
            ['id' => '750e8400-e29b-41d4-a716-446655440009', 'role' => 'Support'],
        ];

        foreach ($lolGameRoles as $data) {
            $gameRole = new GameRole(
                new Uuid($data['id']),
                $roles[$data['role']],
                $games['League of Legends']
            );
            $this->entityManager->persist($gameRole);
        }
    }

    private function createGameRanks(array $games): void
    {
        // League of Legends ranks
        $lolRanks = [
            ['name' => 'Iron', 'level' => 1],
            ['name' => 'Bronze', 'level' => 2],
            ['name' => 'Silver', 'level' => 3],
            ['name' => 'Gold', 'level' => 4],
            ['name' => 'Platinum', 'level' => 5],
            ['name' => 'Diamond', 'level' => 6],
            ['name' => 'Master', 'level' => 7],
            ['name' => 'Grandmaster', 'level' => 8],
            ['name' => 'Challenger', 'level' => 9],
        ];

        foreach ($lolRanks as $index => $rankData) {
            $gameRank = new GameRank(
                new Uuid('550e8400-e29b-41d4-a716-44665544040' . $index),
                $games['League of Legends'],
                $rankData['name'],
                $rankData['level']
            );
            $this->entityManager->persist($gameRank);
        }

        // Valorant ranks
        $valorantRanks = [
            ['name' => 'Iron', 'level' => 1],
            ['name' => 'Bronze', 'level' => 2],
            ['name' => 'Silver', 'level' => 3],
            ['name' => 'Gold', 'level' => 4],
            ['name' => 'Platinum', 'level' => 5],
            ['name' => 'Diamond', 'level' => 6],
            ['name' => 'Immortal', 'level' => 7],
            ['name' => 'Radiant', 'level' => 8],
        ];

        foreach ($valorantRanks as $index => $rankData) {
            $gameRank = new GameRank(
                new Uuid('550e8400-e29b-41d4-a716-44665544050' . $index),
                $games['Valorant'],
                $rankData['name'],
                $rankData['level']
            );
            $this->entityManager->persist($gameRank);
        }
    }

    private function createTestUsers(): void
    {
        // Verificar si ya existen usuarios con el ID de prueba
        $existingUser = $this->entityManager->getRepository(User::class)->find('550e8400-e29b-41d4-a716-446655440001');
        if ($existingUser) {
            return; // Ya existe el usuario de prueba
        }

        // Crear usuario de prueba usando el método create estático si existe
        // Si no, usar el constructor directamente
        // Por ahora vamos a asumir que existe un usuario con ese ID en la base
        // o lo creamos directamente con SQL para evitar complejidad con ValueObjects

        $sql = "INSERT INTO `user` (id, firstname, lastname, username, email, password, profile_image)
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE id=id";

        $this->entityManager->getConnection()->executeStatement($sql, [
            '550e8400-e29b-41d4-a716-446655440001',
            'Test',
            'User',
            'testuser',
            'testuser@jugaenequipo.com',
            '$2y$13$' . str_pad('test', 60, '0'), // Password hash dummy
            ''
        ]);
    }
}
