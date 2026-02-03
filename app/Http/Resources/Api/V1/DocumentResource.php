<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
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
            'title' => $this->title,
            'content' => $this->content,
            'type' => $this->type,
            'file_path' => $this->file_path, // <--- Ensure this is here!
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            // We EXCLUDE 'embedding' here.
        ];
    }
}
