<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\CurrencyConversionException;
use App\Exceptions\ExchangeRateApiException;
use App\Helpers\CacheHelper;
use Exception;

class CurrencyConversionService
{
    public function __construct(readonly private CacheHelper $cache, readonly private ExchangeRateApi $exchangeRateApi)
    {
    }

    /**
     * @throws CurrencyConversionException
     */
    public function convert(string $currencyFrom, string $currencyTo, float $amount): float
    {
        if ($currencyFrom === $currencyTo) {
            return $amount;
        }

        $rates = $this->getRates($currencyFrom);

        if (!$rates) {
            throw new CurrencyConversionException('Unable to retrieve currency rates');
        }

        return round($amount * $rates[$currencyFrom . $currencyTo], 2);
    }

    private function getRates(string $currency): array
    {
        $cachedRates = $this->cache->get($currency);

        if ($cachedRates) {
            return $cachedRates;
        }

        // Ideally, we would pre-warm cache/persist the data in a table via daily/hourly cron
        // therefore, any short-term disruptions won't interrupt the service's functionality
        // meaning that the user never makes direct 3rd party API calls, just uses cached/stored rates.
        // In this case we do persist them, but don't pre-warm it, so the user could encounter failure
        try {
            $liveRates = $this->exchangeRateApi->getRates($currency);
            $this->cache->set($currency, $liveRates, 60 * 60 * 24);
        } catch (ExchangeRateApiException $e) {
            report(new Exception('Exchange rate API failure: ' . $e->getMessage()));

            $this->cache->set($currency, [], 60 * 5);
        }

        return $liveRates ?? [];
    }
}
