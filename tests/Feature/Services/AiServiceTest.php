<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Services\AiService;
use Illuminate\Support\Facades\Log;
use Mockery;
use Prism\Prism\Enums\FinishReason;
use Prism\Prism\Exceptions\PrismException;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Structured\PendingRequest;
use Prism\Prism\Testing\StructuredResponseFake;
use Prism\Prism\ValueObjects\Meta;
use Prism\Prism\ValueObjects\Usage;
use RuntimeException;
use Tests\TestCase;

final class AiServiceTest extends TestCase
{
    public function test_it_returns_structured_response(): void
    {
        $service = new AiService();
        $fakeResponse = StructuredResponseFake::make()
            ->withText(json_encode([
                'answers' => [
                    [
                        'answer' => 'Paris',
                        'is_correct' => true,
                    ],
                    [
                        'answer' => 'Berlin',
                        'is_correct' => false,
                    ],
                ],
                'text' => 'What is the capital of France?',
                'description' => 'A geography question.',
            ], JSON_THROW_ON_ERROR))
            ->withStructured([
                'answers' => [
                    [
                        'answer' => 'Paris',
                        'is_correct' => true,
                    ],
                    [
                        'answer' => 'Berlin',
                        'is_correct' => false,
                    ],
                ],
                'text' => 'What is the capital of France?',
                'description' => 'A geography question.',
            ])
            ->withFinishReason(FinishReason::Stop)
            ->withUsage(new Usage(10, 20))
            ->withMeta(new Meta('fake-1', 'fake-model'));

        Prism::fake([$fakeResponse]);

        $result = $service->getQuestion('Geography', 2);

        $this->assertEquals('What is the capital of France?', $result['text']);
        $this->assertCount(2, $result['answers']);
        $this->assertTrue($result['answers'][0]['is_correct']);
    }

    public function test_it_handles_prism_exception(): void
    {
        $pendingMock = Mockery::mock(PendingRequest::class);
        $pendingMock->shouldReceive('using')->andThrow(new PrismException('API Error'));

        Prism::shouldReceive('structured')->once()->andReturn($pendingMock);

        Log::shouldReceive('error')->once()->withArgs(fn ($message, array $context): bool => str_contains((string) $context['error'], 'API Error'));

        $service = new AiService();
        $result = $service->getQuestion('History', 3);

        $this->assertSame([], $result);
    }

    public function test_it_handles_generic_exception(): void
    {
        $pendingMock = Mockery::mock(PendingRequest::class);
        $pendingMock->shouldReceive('using')->andThrow(new RuntimeException('Unexpected'));

        Prism::shouldReceive('structured')->once()->andReturn($pendingMock);

        Log::shouldReceive('error')->once()->withArgs(fn ($message, array $context): bool => str_contains((string) $context['error'], 'Unexpected'));

        $service = new AiService();
        $result = $service->getQuestion('Science', 3);

        $this->assertSame([], $result);
    }
}
