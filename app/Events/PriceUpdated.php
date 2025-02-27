<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class PriceUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $symbol,
        public float $price,
        public ?float $previous_price,
        public float $last_btc,
        public float $lowest,
        public float $highest,
        public float $daily_change_percentage,
        public array $exchanges,
        public string $trend,
        public ?Carbon $timestamp = null
    ) {
        $this->timestamp = $timestamp ?? now();
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('prices')
        ];
    }

    public function broadcastAs(): string
    {
        return 'PriceUpdated';
    }

    public function broadcastWith(): array
    {
        return [
            'symbol' => $this->symbol,
            'price' => $this->price,
            'previous_price' => $this->previous_price,
            'last_btc' => $this->last_btc,
            'lowest' => $this->lowest,
            'highest' => $this->highest,
            'daily_change_percentage' => $this->daily_change_percentage,
            'exchanges' => $this->exchanges,
            'timestamp' => $this->timestamp->toIso8601String(),
            'trend' => $this->trend,
        ];
    }
}