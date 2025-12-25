<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

final class StoreQuizRequest extends FormRequest
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
            'title' => 'required|string|min:3',
            'description' => 'nullable|string|min:3|max:255',
            'is_work' => [
                'required',
                'boolean',
            ],
            'timer_count' => 'required|integer|min:1',
            'uploaded_image' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'fifty_fifty_hint' => [
                'required',
                'boolean',
            ],
            'can_skip' => [
                'required',
                'boolean',
            ],
            'for_telegram' => [
                'required',
                'boolean',
            ],
        ];
    }
}
