<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Domain\Service;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\Player;

/**
 * Servicio de dominio para verificar el rango de un jugador
 * en las APIs externas de juegos (Riot, Steam, etc.)
 */
interface RankVerifier
{
    /**
     * Verifica el rango/rank actual de un jugador en un juego específico
     *
     * @param Player $player Jugador a verificar
     * @param Uuid $gameId ID del juego
     * @return bool True si la verificación fue exitosa, false en caso contrario
     * @throws RankVerificationException Si hay un error en la verificación
     */
    public function verify(Player $player, Uuid $gameId): bool;

    /**
     * Obtiene información detallada del rango del jugador
     *
     * @param string $username Username del jugador
     * @param string $gameIdentifier Identificador del juego (lol, valorant, cs2, dota2)
     * @return array Array con información del rango: ['rank' => string, 'tier' => string, 'points' => int]
     * @throws RankVerificationException Si hay un error al obtener la información
     */
    public function getRankInfo(string $username, string $gameIdentifier): array;
}

