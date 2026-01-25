<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

final class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->checkRole(UserRole::ADMIN);
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question' => 'required_without_all:uploaded_image,uploaded_audio,image,audio,tmdb_image|nullable|string|min:3',
            'quizzes' => 'nullable|array',
            'answers' => 'required|array|min:2',
            'answers.*.id' => 'required',
            'answers.*.text' => 'required_without:answers.*.image|nullable|string|min:1',
            'answers.*.is_correct' => 'required|boolean',
            'answers.*.image' => 'required_without:answers.*.text|nullable|string',
            'answer_images' => 'nullable|array',
            'uploaded_image' => 'required_without_all:question,image,audio,uploaded_audio,tmdb_image|nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'uploaded_audio' => 'required_without_all:question,image,audio,uploaded_imag,tmdb_image|nullable|mimes:mp3,wav,m4a|max:2048',
            'tmdb_image' => 'nullable|string',
            'image' => 'required_without_all:question,uploaded_image,audio,uploaded_audio,tmdb_image|nullable|string',
            'is_ai' => 'required|boolean',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Example: Extracting a role ID from a combined string
        if ($this->has('answers') && is_string($this->input('answers'))) {
            $this->merge([
                'answers' => json_decode($this->input('answers'), true),
            ]);
        }
    }
}
