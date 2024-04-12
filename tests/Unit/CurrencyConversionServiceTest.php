<?php

namespace Tests\Unit;

use App\Exceptions\CurrencyConversionException;
use App\Helpers\CacheHelper;
use App\Services\CurrencyConversionService;
use App\Services\ExchangeRateApi;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class CurrencyConversionServiceTest extends TestCase
{
    private ExchangeRateApi|MockInterface $mockedApi;
    private CacheHelper|MockInterface $mockedCache;
    private CurrencyConversionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockedApi = Mockery::mock(ExchangeRateApi::class);
        $this->mockedCache = Mockery::mock(CacheHelper::class);

        $this->service = new CurrencyConversionService($this->mockedCache, $this->mockedApi);
    }

    public function testConvertWithEqualCurrenciesDoesntPerformConversion()
    {
        $amount = $this->service->convert('USD', 'USD', 123);

        self::assertEquals(123, $amount);
    }

    public function testConvertWithCachedRatesDoesntCallApi()
    {
        $this->expectNotToPerformAssertions();

        $this->mockedCache->shouldReceive('get')->andReturn(['EURUSD' => 1.05]);
        $this->service->convert('EUR', 'USD', 125);
    }

    public function testConvertWithMissingRatesFetchesAndCachesRates()
    {
        $this->expectNotToPerformAssertions();

        $rates = ['EURUSD' => 1.05];

        $this->mockedCache->shouldReceive('get')->andReturn(null);
        $this->mockedApi->shouldReceive('getRates')->andReturn($rates);
        $this->mockedCache->shouldReceive('set')->withArgs(['EUR', $rates, 60 * 60 * 24]);

        $this->service->convert('EUR', 'USD', 100);
    }

    public function testConvertWithRatesRetrievalFailingThrowsException()
    {
        $this->mockedCache->shouldReceive('get')->andReturn(null);
        $this->mockedCache->shouldReceive('set');
        $this->mockedApi->shouldReceive('getRates')->andReturn([]);

        self::expectException(CurrencyConversionException::class);

        $this->service->convert('USD', 'EUR', 123);
    }

    public function testConvertPerformsConversionCorrectly()
    {
        $rates = ['EURUSD' => 1.05];
        $expectedAmount = round(100 * $rates['EURUSD'], 2);

        $this->mockedCache->shouldReceive('get')->andReturn(null);
        $this->mockedApi->shouldReceive('getRates')->andReturn($rates);
        $this->mockedCache->shouldReceive('set')->withArgs(['EUR', $rates, 60 * 60 * 24]);

        $amount = $this->service->convert('EUR', 'USD', 100);

        self::assertEquals($expectedAmount, $amount);
    }
}
