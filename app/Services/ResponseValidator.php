<?php

namespace App\Services;

use App\Exceptions\FreeCryptoApiClient\FreeCryptoApiRateLimitException;
use App\Exceptions\FreeCryptoApiClient\FreeCryptoApiInvalidResponseException;

/**
 * - SOLID (Single Responsibility): This class only validates API responses and throws exceptions (validateResponse()). This keeps validation logic focused and testable.
 * - DRY: The validateResponse() method is reused for all API requests, centralizing validation logic.
 * - YAGNI: Only validates what is needed for the current API contract; no extra validation rules or error types.
 */
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
