<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\Game;

final readonly class GameProcessAnswerDTO
{
    public function __construct(
        public int $givenAnswer,
        public Game $game,
        public bool $fiftyFiftyHint,
        public bool $canSkip,
        public bool $next = false,
        public bool $isTelegram = false,
        public string $answerManualInput = ''
    ) {}
}
