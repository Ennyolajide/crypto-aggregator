<?php

namespace App\Services;

use App\Exceptions\FreeCryptoApiClient\FreeCryptoApiRateLimitException;
use App\Exceptions\FreeCryptoApiClient\FreeCryptoApiInvalidResponseException;

class ResponseValidator
{
    /**
     * Validate API response and check for errors.
     *
     * @param \Illuminate\Http\Client\Response $response
     * @param array $request
     * @throws FreeCryptoApiRateLimitException|FreeCryptoApiInvalidResponseException
     */
    public function validateResponse($response, array $request)
    {
        if (!$response->successful()) {
            if ($response->status() === 429) {
                throw new FreeCryptoApiRateLimitException($request['exchange']);
            }
            throw new FreeCryptoApiInvalidResponseException($request['exchange'], 'Unexpected API response.');
        }
        return $response;
    }
}
