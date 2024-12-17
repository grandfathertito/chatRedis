<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'content' => decryptMessage($this->content),
            'sender' => $this->sender->name,
            'receiver' => $this->receiver->name,
            'receiver_id' => $this->receiver->id,
            'send_date' => Carbon::parse($this->created_at)->format('d-m-Y H:i'),
            'was_seen' => $this->seen_at ? Carbon::parse($this->seen_at)->format('d-m-Y H:i') : null,
        ];
    }
}
