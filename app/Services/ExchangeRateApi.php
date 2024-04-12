<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ExchangeRateApiException;
use GuzzleHttp\Client;
use Throwable;

class ExchangeRateApi
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @throws ExchangeRateApiException
     */
    public function getRates(string $currency): array
    {
        try {
            $response = $this->client->get('http://api.exchangerate.host/live?access_key=' . env('CURRENCY_EXCHANGE_API_KEY') . '&source=' . $currency);
            $data = json_decode($response->getBody()->getContents(), true);
        } catch (Throwable $e) {
            throw new ExchangeRateApiException($e->getMessage());
        }

        return $data['quotes'] ?? [];
    }
}
