<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PopularQuizzesRequest extends FormRequest
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
            'start' => [
                'required_with:end',
                Rule::date()->format('Y-m-d'),
            ],
            'end' => [
                'required_with:start',
                Rule::date()->format('Y-m-d'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'start.required_with' => 'The start date is required.',
            'start.date_format' => 'The start date is required.',
            'end.required_with' => 'The end date is required.',
            'end.date_format' => 'The end date is required.',
        ];
    }
}
