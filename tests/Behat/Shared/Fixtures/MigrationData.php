<?php

declare(strict_types=1);

namespace App\Tests\Behat\Shared\Fixtures;

/**
 * IDs de datos de referencia creados por migraciones.
 * Estos datos SON READ-ONLY y NO deben modificarse ni eliminarse por los tests.
 *
 * Fuente: migrations/Version20251116054200.php
 */
final class MigrationData
{
    // ========== GAMES (migración) ==========
    public const GAME_VALORANT_ID = '550e8400-e29b-41d4-a716-446655440080';
    public const GAME_LOL_ID = '550e8400-e29b-41d4-a716-446655440081';
    public const GAME_CS2_ID = '550e8400-e29b-41d4-a716-446655440082';
    public const GAME_DOTA2_ID = '550e8400-e29b-41d4-a716-446655440083';

    // ========== ROLES VALORANT (migración) ==========
    public const ROLE_DUELIST_ID = '650e8400-e29b-41d4-a716-446655440001';
    public const ROLE_CONTROLLER_ID = '650e8400-e29b-41d4-a716-446655440002';
    public const ROLE_SENTINEL_ID = '650e8400-e29b-41d4-a716-446655440003';
    public const ROLE_INITIATOR_ID = '650e8400-e29b-41d4-a716-446655440004';

    // ========== ROLES LEAGUE OF LEGENDS (migración) ==========
    public const ROLE_TOP_ID = '650e8400-e29b-41d4-a716-446655440005';
    public const ROLE_JUNGLE_ID = '650e8400-e29b-41d4-a716-446655440006';
    public const ROLE_MID_ID = '650e8400-e29b-41d4-a716-446655440007';
    public const ROLE_ADC_ID = '650e8400-e29b-41d4-a716-446655440008';
    public const ROLE_SUPPORT_ID = '650e8400-e29b-41d4-a716-446655440009';

    // ========== RANKS (migración) ==========
    public const RANK_UNRANKED_ID = '00000000-0000-0000-0000-000000000001';
    public const RANK_IRON_ID = '950e8400-e29b-41d4-a716-446655440001';
    public const RANK_BRONZE_ID = '950e8400-e29b-41d4-a716-446655440002';
    public const RANK_SILVER_ID = '950e8400-e29b-41d4-a716-446655440003';
    public const RANK_GOLD_ID = '950e8400-e29b-41d4-a716-446655440004';
    public const RANK_PLATINUM_ID = '950e8400-e29b-41d4-a716-446655440005';
    public const RANK_EMERALD_ID = '950e8400-e29b-41d4-a716-446655440006';
    public const RANK_DIAMOND_ID = '950e8400-e29b-41d4-a716-446655440007';
    public const RANK_ASCENDANT_ID = '950e8400-e29b-41d4-a716-446655440008';
    public const RANK_IMMORTAL_ID = '950e8400-e29b-41d4-a716-446655440009';
    public const RANK_RADIANT_ID = '950e8400-e29b-41d4-a716-446655440010';

    // ========== GAME ROLES VALORANT (migración) ==========
    public const GAME_ROLE_VALORANT_DUELIST_ID = '750e8400-e29b-41d4-a716-446655440001';
    public const GAME_ROLE_VALORANT_CONTROLLER_ID = '750e8400-e29b-41d4-a716-446655440002';
    public const GAME_ROLE_VALORANT_SENTINEL_ID = '750e8400-e29b-41d4-a716-446655440003';
    public const GAME_ROLE_VALORANT_INITIATOR_ID = '750e8400-e29b-41d4-a716-446655440004';

    // ========== GAME ROLES LEAGUE OF LEGENDS (migración) ==========
    public const GAME_ROLE_LOL_TOP_ID = '750e8400-e29b-41d4-a716-446655440005';
    public const GAME_ROLE_LOL_JUNGLE_ID = '750e8400-e29b-41d4-a716-446655440006';
    public const GAME_ROLE_LOL_MID_ID = '750e8400-e29b-41d4-a716-446655440007';
    public const GAME_ROLE_LOL_ADC_ID = '750e8400-e29b-41d4-a716-446655440008';
    public const GAME_ROLE_LOL_SUPPORT_ID = '750e8400-e29b-41d4-a716-446655440009';

    // ========== GAME RANKS VALORANT (migración) - Ejemplos ==========
    public const GAME_RANK_VALORANT_IRON_1_ID = '850e8400-e29b-41d4-a716-446655440001';
    public const GAME_RANK_VALORANT_BRONZE_1_ID = '850e8400-e29b-41d4-a716-446655440004';
    public const GAME_RANK_VALORANT_SILVER_1_ID = '850e8400-e29b-41d4-a716-446655440007';
    public const GAME_RANK_VALORANT_GOLD_1_ID = '850e8400-e29b-41d4-a716-446655440010';
    public const GAME_RANK_VALORANT_GOLD_2_ID = '850e8400-e29b-41d4-a716-446655440011';
    public const GAME_RANK_VALORANT_GOLD_3_ID = '850e8400-e29b-41d4-a716-446655440012';
    public const GAME_RANK_VALORANT_PLATINUM_1_ID = '850e8400-e29b-41d4-a716-446655440013';
    public const GAME_RANK_VALORANT_DIAMOND_1_ID = '850e8400-e29b-41d4-a716-446655440016';

    // ========== NOTIFICATION TYPES (migración) ==========
    public const NOTIFICATION_TYPE_NEW_MESSAGE_ID = '550e8400-e29b-41d4-a716-446655440099';
    public const NOTIFICATION_TYPE_POST_LIKED_ID = '850e8400-e29b-41d4-a716-446655440001';
    public const NOTIFICATION_TYPE_POST_COMMENTED_ID = '850e8400-e29b-41d4-a716-446655440002';
    public const NOTIFICATION_TYPE_POST_SHARED_ID = '750e8400-e29b-41d4-a716-446655440002';
    public const NOTIFICATION_TYPE_NEW_FOLLOWER_ID = '750e8400-e29b-41d4-a716-446655440001';

    // ========== TOURNAMENT STATUSES (migración) ==========
    // NOTE: After Version20251119000002.php migration, these are now UUIDs instead of strings
    public const TOURNAMENT_STATUS_CREATED_ID = 'a50e8400-e29b-41d4-a716-446655440001';
    public const TOURNAMENT_STATUS_ACTIVE_ID = 'a50e8400-e29b-41d4-a716-446655440002';
    public const TOURNAMENT_STATUS_DELETED_ID = 'a50e8400-e29b-41d4-a716-446655440003';
    public const TOURNAMENT_STATUS_ARCHIVED_ID = 'a50e8400-e29b-41d4-a716-446655440004';
    public const TOURNAMENT_STATUS_FINALIZED_ID = 'a50e8400-e29b-41d4-a716-446655440005';
    public const TOURNAMENT_STATUS_SUSPENDED_ID = 'a50e8400-e29b-41d4-a716-446655440006';

    private function __construct()
    {
        // Prevent instantiation
    }
}
