<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Exceptions\PrismException;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Schema\ArraySchema;
use Prism\Prism\Schema\BooleanSchema;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Schema\StringSchema;
use Throwable;

final readonly class AiService
{
    public function getQuestion(string $theme, int $number_of_options): array
    {
        $prompt = view('prompts.generate_question', [
            'theme' => $theme,
            'number_of_options' => $number_of_options,
        ])->render();
        try {
            $response = Prism::structured()
                ->using(Provider::Gemini, config('services.prism.gemini.model'))
                ->withSchema($this->getSchemaQuestion())
                ->withPrompt($prompt)
                ->asStructured();

            return $response->structured;
        } catch (PrismException $e) {
            Log::error('Text generation failed:', ['error' => $e->getMessage()]);
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        } catch (Throwable $e) {
            Log::error('Generic error:', ['error' => $e->getMessage()]);
            throw new Exception('An unexpected error occurred while generating the question.', $e->getCode(), $e);
        }

        return [];
    }

    public function getSchemaQuestion(): ObjectSchema
    {
        return new ObjectSchema(
            name: 'question_response',
            description: 'A structured representation of a quiz question with answer options.',
            properties: [
                new StringSchema('text', 'Question text'),
                new StringSchema('description', 'Brief explanation', true),
                new ArraySchema('answers', 'The list of answer options', new ObjectSchema(
                    'answer_option',
                    'An answer option for the quiz question',
                    properties: [
                        new StringSchema('answer', 'The text of the answer option'),
                        new BooleanSchema('is_correct', 'Indicates if this option is correct'),
                    ],
                    requiredFields: ['answer', 'is_correct'],
                )),
            ],
            requiredFields: ['text', 'answers']
        );
    }
}
