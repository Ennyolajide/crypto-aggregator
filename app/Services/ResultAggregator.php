<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ResultAggregator
{
    /**
     * Process API responses and aggregate the results.
     */
    public function processResponses(array $responses, array $requests): array
    {
        $symbolData = [];

        foreach ($responses as $index => $response) {
            $request = $requests[$index];
            $symbol = $request['symbol'];
            $exchange = $request['exchange'];

            try {
                if ($response['state'] !== 'fulfilled') {
                    continue; // Skip failed responses
                }

                $data = $response['value']->json();
                if (empty($data['symbols'][0])) {
                    continue; // Skip empty responses
                }

                $price = $data['symbols'][0];
                $symbolData[$symbol]['prices'][$exchange] = (float)$price['last'];
                $symbolData[$symbol]['last_btc'][$exchange] = (float)$price['last_btc'];
                $symbolData[$symbol]['lowest'][$exchange] = (float)$price['lowest'];
                $symbolData[$symbol]['highest'][$exchange] = (float)$price['highest'];
                $symbolData[$symbol]['daily_changes'][$exchange] = (float)$price['daily_change_percentage'];
                $symbolData[$symbol]['exchanges'][] = $exchange;
                $symbolData[$symbol]['dates'][$exchange] = $price['date'];
            } catch (\Exception $e) {
                Log::error("Failed to process response for {$symbol} at {$exchange}", [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $this->formatResults($symbolData);
    }

    /**
     * Format aggregated results for easier consumption.
     */
    private function formatResults(array $symbolData): array
    {
        return array_values(array_map(fn ($symbol, $data) => [
            'symbol' => $symbol,
            'price' => array_sum($data['prices']) / count($data['prices']),
            'last_btc' => array_sum($data['last_btc']) / count($data['last_btc']),
            'lowest' => min($data['lowest']),
            'highest' => max($data['highest']),
            'daily_change_percentage' => array_sum($data['daily_changes']) / count($data['daily_changes']),
            'exchanges' => array_unique($data['exchanges']),
            'timestamp' => max($data['dates'])
        ], array_keys($symbolData), $symbolData));
    }
}
