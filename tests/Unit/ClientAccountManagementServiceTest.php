<?php

namespace Tests\Unit;

use App\Exceptions\ClientAccountException;
use App\Models\ClientAccount;
use App\Repositories\ClientAccountRepository;
use App\Services\ClientAccountManagementService;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class ClientAccountManagementServiceTest extends TestCase
{
    private ClientAccountRepository|MockInterface $mockedRepo;
    private ClientAccountManagementService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockedRepo = Mockery::mock(ClientAccountRepository::class);

        $this->service = new ClientAccountManagementService($this->mockedRepo);
    }

    public function testReduceBalanceWithInvalidAccountThrowsException()
    {
        $this->mockedRepo->shouldReceive('getAccountById')->andReturnNull();

        self::expectException(ClientAccountException::class);
        self::expectExceptionMessage('Account not found!');

        $this->service->reduceBalance(123, 456);
    }

    public function testReduceBalanceWithInvalidAvailableBalanceThrowsException()
    {
        $account = new ClientAccount();
        $account->balance = 120;

        $this->mockedRepo->shouldReceive('getAccountById')->andReturn($account);

        self::expectException(ClientAccountException::class);
        self::expectExceptionMessage('Available balance is too low!');

        $this->service->reduceBalance(123, 121);
    }

    public function testReduceBalanceWithEnoughAvailableBalanceSucceeds()
    {
        $account = new ClientAccount();
        $account->balance = 120;

        $this->mockedRepo->shouldReceive('getAccountById')->andReturn($account);
        $this->mockedRepo->shouldReceive('store')->andReturn($account);

        $updatedClientAccount = $this->service->reduceBalance(123, 119);

        self::assertEquals(1, $updatedClientAccount->balance);
    }

    public function testIncreaseBalanceWithInvalidAccountThrowsException()
    {
        $this->mockedRepo->shouldReceive('getAccountById')->andReturnNull();

        self::expectException(ClientAccountException::class);
        self::expectExceptionMessage('Account not found!');

        $this->service->increaseBalance(123, 456);
    }

    public function testIncreaseBalanceWithValidAccountSucceeds()
    {
        $account = new ClientAccount();
        $account->balance = 12;
        $this->mockedRepo->shouldReceive('getAccountById')->andReturn($account);
        $this->mockedRepo->shouldReceive('store')->andReturn($account);

        $updatedAccount = $this->service->increaseBalance(123, 38);

        self::assertEquals(50, $updatedAccount->balance);
    }
}
