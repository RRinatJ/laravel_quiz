<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class LogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'name' => $this->causer->name ?? 'Unknown',
            ],
            'description' => $this->description,
            'changes' => $this->attribute_changes,
            'properties' => $this->properties,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
