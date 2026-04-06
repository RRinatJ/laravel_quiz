<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class QuizResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $data['tags'] = $this->tags->map(fn ($tag): array => [
            'id' => $tag->id,
            'name' => $tag->getTranslation('name', app()->getLocale()),
        ])->toArray();

        return $data;
    }
}
