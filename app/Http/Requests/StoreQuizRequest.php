<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreQuizRequest extends FormRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:3',
            'is_work' => [
                'required',
                'boolean',
            ],
            'timer_count' => 'required|integer',
            'uploaded_image' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }
}
