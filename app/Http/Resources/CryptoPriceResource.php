<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CryptoPriceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'symbol' => strtoupper($this->symbol),
            'price' => (float) $this->price,
            'previous_price' => (float) $this->previous_price,
            'last_btc' => (float) $this->last_btc,
            'lowest' => (float) $this->lowest,
            'highest' => (float) $this->highest,
            'daily_change_percentage' => (float) $this->daily_change_percentage,
            'exchanges' => $this->exchanges ?? [],
            'timestamp' => $this->created_at->toIso8601String(),
            'formatted_time' => $this->created_at->format('H:i:s'),
            'trend' => $this->trend,
        ];
    }
}
