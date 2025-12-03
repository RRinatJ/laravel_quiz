<?php

declare(strict_types=1);

namespace App\Services\Telegram;

use App\Models\Game;
use App\Services\GameService;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Keyboard\Keyboard;

final readonly class TelegramGameService
{
    public function __construct(
        private GameService $gameService,
        private Api $telegram,
    ) {}

    public function startMessage(int $chat_id, $quiz_list): void
    {
        $keyboard = Keyboard::make()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true);

        foreach ($quiz_list as $quiz) {
            $keyboard->row(
                [
                    Keyboard::button(['text' => $quiz->title]),
                ]
            );
        }

        $this->telegram->sendMessage([
            'chat_id' => $chat_id,
            'text' => 'Welcome to the Quiz Game! Press the button below to start.',
            'reply_markup' => $keyboard,
        ]);
    }

    public function startGame(int $chat_id, int $quiz_id): void
    {
        $game = $this->gameService->createGame($quiz_id, $chat_id);
        $game->load('quiz', 'question.answers', 'latestStep');
        $game_data = $this->gameService->show($game);

        $this->processMessage($game, $game_data, $chat_id);
    }

    public function processAnswer(int $chat_id, string $callback_data): void
    {

        $game = Game::with('quiz', 'question.answers')->where('chat_id', $chat_id)
            ->latest()
            ->first();

        $answer_id = is_numeric($callback_data) ? (int) $callback_data : 0;

        $fifty_fifty_hint = $callback_data === '50/50';
        $can_skip = $callback_data === 'Skip question';

        $this->gameService->processAnswer(
            $answer_id,
            $game,
            $fifty_fifty_hint,
            $can_skip
        );

        $game->refresh();
        $game_data = $this->gameService->show($game, [], $fifty_fifty_hint);
        if ($game_data['error'] !== '') {
            $this->sendMessage($chat_id, $game_data['error'].' Start a new game? /start');

            return;
        }
        if ($game_data['message'] !== '') {
            $this->sendMessage($chat_id, $game_data['message'].' Start a new game? /start');

            return;
        }

        $this->processMessage($game, $game_data, $chat_id);
    }

    public function processMessage(Game $game, array $game_data, int $chat_id): void
    {
        $reply_markup = $this->getAnswers($game, $game_data['answers']);

        if ($game->question->image !== null) {
            $image_path = Storage::disk('public')->path($game->question->image);
            $this->sendPhoto(
                $chat_id,
                InputFile::create($image_path, basename($game->question->image)),
                $game->question->question,
                $reply_markup
            );
        } else {
            $this->sendMessage(
                $chat_id,
                $game->question->question,
                $reply_markup
            );
        }
    }

    public function sendPhoto(int $chat_id, InputFile $image_path, ?string $caption, Keyboard $reply_markup): void
    {
        if (is_null($caption)) {
            $caption = '';
        }
        $this->telegram->sendPhoto([
            'chat_id' => $chat_id,
            'photo' => $image_path,
            'caption' => $caption,
            'reply_markup' => $reply_markup,
        ]);
    }

    public function sendMessage(int $chat_id, string $text, ?Keyboard $reply_markup = null): \Telegram\Bot\Objects\Message
    {
        $params = [
            'chat_id' => $chat_id,
            'text' => $text,
        ];
        if ($reply_markup instanceof Keyboard) {
            $params['reply_markup'] = $reply_markup;
        }

        return $this->telegram->sendMessage($params);
    }

    public function getAnswers(Game $game, array $answers): Keyboard
    {
        $reply_markup = Keyboard::make()
            ->inline()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true);

        foreach ($answers as $answer) {
            $reply_markup->row([
                Keyboard::inlineButton([
                    'text' => $answer['text'],
                    'callback_data' => $answer['id'],
                ]),
            ]);
        }
        if ($game->fifty_fifty_hint) {
            $reply_markup->row([
                Keyboard::inlineButton([
                    'text' => 'Hint - 50/50',
                    'callback_data' => '50/50',
                ]),
            ]);
        }
        if ($game->can_skip) {
            $reply_markup->row([
                Keyboard::inlineButton([
                    'text' => 'Hint - Skip question',
                    'callback_data' => 'Skip question',
                ]),
            ]);
        }

        return $reply_markup;
    }
}
