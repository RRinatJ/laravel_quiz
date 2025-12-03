<?php

declare(strict_types=1);

namespace Tests\Feature\Telegram;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Services\GameService;
use App\Services\Telegram\TelegramGameService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;
use Tests\TestCase;

final class TelegramControllerTest extends TestCase
{
    use RefreshDatabase;

    private Api $telegramMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Мокаем Telegram SDK
        $this->telegramMock = Mockery::mock(Api::class);
        $this->app->instance(Api::class, $this->telegramMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function test_it_handles_start_command_correctly(): void
    {
        // Arrange
        $chatId = 123456789;
        Quiz::factory()->count(3)->create([
            'is_work' => true,
            'for_telegram' => true,
        ]);

        // Создаём мок Update с /start
        $update = $this->createUpdateWithMessage($chatId, '/start');

        $this->telegramMock
            ->shouldReceive('getWebhookUpdate')
            ->once()
            ->andReturn($update);

        $this->telegramMock
            ->shouldReceive('sendMessage')
            ->once();

        // Act
        $response = $this->postJson('/telegram/webhook');

        // Assert
        $response->assertStatus(200)
            ->assertJson(['status' => 'ok']);
    }

    /** @test */
    public function test_it_starts_quiz_when_user_sends_exact_quiz_title(): void
    {
        $chatId = 123456789;
        Quiz::factory()
            ->has(Question::factory()->count(1))
            ->create([
                'title' => 'History',
                'is_work' => true,
                'for_telegram' => true,
            ]);

        $update = $this->createUpdateWithMessage($chatId, 'History');

        $this->telegramMock
            ->shouldReceive('getWebhookUpdate')
            ->once()
            ->andReturn($update);

        $this->telegramMock
            ->shouldReceive('sendMessage')
            ->once();

        $response = $this->postJson('/telegram/webhook');

        $response->assertOk()
            ->assertJson(['status' => 'ok']);
    }

    /** @test */
    public function test_it_handles_callback_query_for_answer(): void
    {
        $chatId = 123456789;
        $callbackQueryId = 'callback_12345';

        $quiz = Quiz::factory()
            ->has(Question::factory()->has(Answer::factory()->count(2))->count(1))
            ->create([
                'title' => 'History',
                'is_work' => true,
                'for_telegram' => true,
            ]);

        $this->telegramMock
            ->shouldReceive('sendMessage')
            ->twice();

        $gameService = new GameService();
        $service = new TelegramGameService($gameService, $this->telegramMock);
        $service->startGame($chatId, $quiz->id);

        $callbackData = (string) Answer::query()->first()->id;

        $update = Update::make([
            'callback_query' => [
                'id' => $callbackQueryId,
                'from' => ['id' => 555],
                'message' => [
                    'chat' => ['id' => $chatId, 'type' => 'private'],
                    'message_id' => 999,
                ],
                'data' => $callbackData,
            ],
        ]);

        $this->telegramMock
            ->shouldReceive('getWebhookUpdate')
            ->once()
            ->andReturn($update);

        $this->telegramMock
            ->shouldReceive('answerCallbackQuery')
            ->once()
            ->with([
                'callback_query_id' => $callbackQueryId,
            ]);

        $response = $this->postJson('/telegram/webhook');

        $response->assertOk();
    }

    /** @test */
    public function test_it_returns_ok_even_on_unknown_message(): void
    {
        $update = $this->createUpdateWithMessage(555666777, 'какая-то фигня');

        $this->telegramMock
            ->shouldReceive('getWebhookUpdate')
            ->once()
            ->andReturn($update);

        // Ничего не ожидаем от сервиса — просто проверяем, что не падает
        $response = $this->postJson('/telegram/webhook');

        $response->assertOk()
            ->assertJson(['status' => 'ok']);
    }

    /** @test */
    public function test_it_logs_error_and_returns_ok_on_exception(): void
    {
        $this->telegramMock
            ->shouldReceive('getWebhookUpdate')
            ->once()
            ->andThrow(new Exception('Boom!'));

        Log::shouldReceive('error')
            ->once()
            ->with('Telegram webhook error', Mockery::any());

        $response = $this->postJson('/telegram/webhook');

        $response->assertStatus(200)
            ->assertJson(['status' => 'error']);
    }

    private function createUpdateWithMessage(int $chatId, string $text): Update
    {
        return Update::make([
            'message' => [
                'chat' => ['id' => $chatId, 'type' => 'private'],
                'text' => $text,
                'message_id' => 123,
                'from' => ['id' => 999],
            ],
        ]);
    }
}
