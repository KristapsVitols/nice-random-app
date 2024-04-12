<?php

namespace Tests\Unit;

use App\Exceptions\ClientAccountException;
use App\Models\ClientAccount;
use App\Repositories\ClientAccountRepository;
use App\Services\AccountRetrievalService;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class AccountRetrievalServiceTest extends TestCase
{
    private ClientAccountRepository|MockInterface $mockedRepo;
    private AccountRetrievalService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockedRepo = Mockery::mock(ClientAccountRepository::class);

        $this->service = new AccountRetrievalService($this->mockedRepo);
    }

    public function testGetAccountsByClientIdWithMissingIdThrowsException()
    {
        self::expectException(ClientAccountException::class);
        self::expectExceptionMessage('Please provide a client id!');

        $this->service->getAccountsByClientId(0);
    }

    public function testGetAccountsByClientIdWithIdReturnsAccountsSuccesfully()
    {
        $accountOne = new ClientAccount();
        $accountOne->id = 1;
        $accountOne->client_id = 2;
        $accountOne->balance = 123;
        $accountOne->currency = 'USD';

        $accountTwo = new ClientAccount();
        $accountTwo->id = 2;
        $accountTwo->client_id = 2;
        $accountTwo->balance = 444;
        $accountTwo->currency = 'EUR';

        $this->mockedRepo->shouldReceive('getAccountsByClientId')->andReturn([$accountOne, $accountTwo]);

        $accounts = $this->service->getAccountsByClientId(123);

        self::assertCount(2, $accounts);
        self::assertArrayHasKey('accountId', $accounts[0]); // Converted toApiData()
    }
}
