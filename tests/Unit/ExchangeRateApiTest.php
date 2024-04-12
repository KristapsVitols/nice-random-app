<?php

namespace Tests\Unit;

use App\Exceptions\ExchangeRateApiException;
use App\Services\ExchangeRateApi;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ExchangeRateApiTest extends TestCase
{
    private Client|MockInterface $mockedClient;
    private ExchangeRateApi $api;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockedClient = Mockery::mock(Client::class);

        $this->api = new ExchangeRateApi($this->mockedClient);
    }

    public function testGetRatesOnApiFailureThrowsException()
    {
        $this->mockedClient->shouldReceive('get')->andThrow(BadRequestException::class);
        self::expectException(ExchangeRateApiException::class);

        $this->api->getRates('USD');
    }

    public function testGetRatesOnApiSuccessReturnsData()
    {
        $quotes = ['EURUSD' => 1.0523];

        $this->mockedClient->shouldReceive('get')->andReturn(
            new Response(status: 200, body: json_encode(['quotes' => $quotes]))
        );

        $rates = $this->api->getRates('EUR');

        self::assertEquals($quotes, $rates);
    }
}
