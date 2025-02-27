<?php

namespace App\DataTransfers;

class CryptoPriceData
{
    public function __construct(
        public readonly string $symbol,
        public readonly float $price,
        public readonly float $lastBtc,
        public readonly float $lowest,
        public readonly float $highest,
        public readonly float $dailyChangePercentage,
        public readonly string $date,
        public readonly array $exchanges
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            symbol: $data['symbol'],
            price: $data['price'],
            lastBtc: $data['last_btc'],
            lowest: $data['lowest'],
            highest: $data['highest'],
            dailyChangePercentage: $data['daily_change_percentage'],
            date: $data['date'],
            exchanges: $data['exchanges']
        );
    }

    public function toArray(): array
    {
        return [
            'symbol' => $this->symbol,
            'price' => $this->price,
            'last_btc' => $this->lastBtc,
            'lowest' => $this->lowest,
            'highest' => $this->highest,
            'daily_change_percentage' => $this->dailyChangePercentage,
            'date' => $this->date,
            'exchanges' => $this->exchanges
        ];
    }
} 