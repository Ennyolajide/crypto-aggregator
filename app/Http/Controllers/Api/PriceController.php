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
     * Get latest prices for all pairs
     */
    public function latest()
    {
        $prices = CryptoPrice::query()
            ->latestBySymbol()
            ->get();

        return CryptoPriceResource::collection($prices);
    }

    /**
     * Get historical prices for a specific pair
     */
    public function history(string $pair): JsonResponse
    {
        // Cache the history for performance
        $history = Cache::remember("price-history-{$pair}", 60, function () use ($pair) {
            return CryptoPrice::where('symbol', $pair) // Ensure we're using 'symbol'
                ->latest('created_at') // Use 'created_at' instead of 'timestamp'
                ->limit(100)
                ->get()
                ->map(fn($price) => [
                    'price' => (float) $price->price,
                    'timestamp' => $price->created_at->toIso8601String(), // Use created_at
                ]);
        });
        return response()->json($history);
    }

}