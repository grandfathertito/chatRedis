<?php

namespace App\Http\Resources\AuthResource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'token' => $this->token,
            'register_date' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
