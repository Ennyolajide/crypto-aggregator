<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CryptoPrice extends Model
{
    use HasFactory;

    protected $table = 'crypto_prices';

    protected $fillable = [
        'symbol',
        'price',
        'previous_price',
        'last_btc',
        'lowest',
        'highest',
        'daily_change_percentage',
        'exchanges',
        'trend',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'price' => 'float',
        'previous_price' => 'float',
        'last_btc' => 'float',
        'lowest' => 'float',
        'highest' => 'float',
        'daily_change_percentage' => 'float',
        'exchanges' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the latest price for each symbol
     */
    public static function getLatestPrices(): array
    {
        return static::query()
            ->select('symbol', 'price', 'last_btc', 'lowest', 'highest', 
                    'daily_change_percentage', 'exchanges', 'timestamp')
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('crypto_prices')
                    ->groupBy('symbol');
            })
            ->get()
            ->keyBy('symbol')
            ->toArray();
    }

    /**
     * Get price change percentage
     */
    public function getPriceChangeAttribute(): float
    {
        if (!$this->previous_price || $this->previous_price == 0) {
            return 0;
        }

        return (($this->price - $this->previous_price) / $this->previous_price) * 100;
    }

    /**
     * Get price trend (up/down/neutral)
     */
    public function getPriceTrendAttribute(): string
    {
        if (!$this->previous_price) {
            return 'neutral';
        }
        return $this->price > $this->previous_price ? 'up' : 'down';
    }

    /**
     * Scope for getting recent prices within timeframe
     */
    public function scopeRecent(Builder $query, int $seconds = 300): Builder
    {
        return $query->where('created_at', '>=', now()->subSeconds($seconds));
    }

    /**
     * Scope for getting latest price for each symbol
     */
    public function scopeLatestBySymbol(Builder $query)
    {
        return $query->whereIn('id', function ($subquery) {
            $subquery->selectRaw('MAX(id)')
                ->from('crypto_prices')
                ->groupBy('symbol');
        });
    }
}