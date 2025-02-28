<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CryptoPrice;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\CryptoPriceResource;
use Illuminate\Http\JsonResponse;

class PriceController extends Controller
{
    /**
     * Get the latest prices for all pairs.
     * 
     * Caches results for 30 seconds to improve performance.
     */
    public function latest(): JsonResponse
    {
        $prices = Cache::remember('latest-crypto-prices', 30, function () {
            return CryptoPrice::query()
                ->latestBySymbol()
                ->get();
        });

        return response()->json([
            'prices' => CryptoPriceResource::collection($prices),
            'lastUpdate' => now()->toIso8601String()
        ]);
    }

    /**
     * Get historical prices for a specific pair.
     * 
     * @param string $pair Crypto symbol (e.g., BTC/USDT)
     */
    public function history(string $pair): JsonResponse
    {
        // Cache the history for 60 seconds
        $history = Cache::remember("price-history:{$pair}", 60, function () use ($pair) {
            return CryptoPrice::where('symbol', strtoupper($pair)) // Ensure uppercase consistency
                ->latest('created_at')
                ->limit(100)
                ->get();
        });

        return response()->json([
            'symbol' => strtoupper($pair),
            'history' => CryptoPriceResource::collection($history)
        ]);
    }
}
