<?php

declare(strict_types=1);

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Services\Telegram\TelegramGameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Throwable;

final class TelegramController extends Controller
{
    public function __construct(
        private readonly TelegramGameService $gameService,
        private readonly Api $telegram,
    ) {}

    public function webhook(Request $request): JsonResponse
    {

        try {
            $update = $this->telegram->getWebhookUpdate();
            if ($message = $update->getMessage()) {

                $quiz_list = Quiz::query()
                    ->select('id', 'title')
                    ->where('is_work', true)
                    ->where('for_telegram', true)
                    ->whereHas('questions')
                    ->get();

                $chatId = $message->getChat()->getId();
                $text = $message->getText();
                $quiz = $quiz_list->firstWhere('title', $text);
                if ($text === '/start') {
                    $this->gameService->startMessage($chatId, $quiz_list);
                } elseif ($quiz !== null) {
                    $this->gameService->startGame($chatId, $quiz->id);
                } elseif ($update->isType('callback_query')) {
                    $callbackQuery = $update->getCallbackQuery();
                    $callback_data = $callbackQuery->getData();
                    $this->gameService->processAnswer($chatId, $callback_data);
                    $this->telegram->answerCallbackQuery([
                        'callback_query_id' => $callbackQuery->getId(),
                    ]);
                }
            }
        } catch (Throwable $e) {
            Log::error('Telegram webhook error', [
                'exception' => $e->getMessage(),
                'update' => $request->all(),
            ]);

            return response()->json(['status' => 'error'], 200);
        }

        return response()->json(['status' => 'ok']);
    }
}
